<?php

namespace AppBundle\Service\DataManager;

use AppBundle\Entity\Account;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\JournalTransaction;
use AppBundle\Entity\RefType;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;

class JournalTransactionManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    public function updateJournalTransactions(Corporation $corporation, $fromID = null){
        $apiKey = $this->getApiKey($corporation);

        $client = $this->getClient($apiKey);

        $accounts = $corporation->getAccounts();

        foreach($accounts as $acc){
            $params = $this->buildTransactionParams($acc, $fromID);
            $transactions = $client->WalletJournal($params);

            $this->mapList($transactions, [ 'corp' => $corporation, 'acc' => $acc]);
        }
    }

    public function mapList($items, array $options) {
        $corp = isset($options['corp']) ? $options['corp'] : false;
        $acc = isset($options['acc']) ? $options['acc']: false;

        $em  = $this->doctrine->getManager();

        if (!$corp instanceof Corporation || !$acc instanceof Account) {
            throw new OptionDefinitionException(sprintf('Option corp required and must by of type %s', get_class(new Corporation())));
        }

        $count = 0;
        $notValid = false;
        while (!$notValid || $count <= count($items->entries)-1) {
            $t = isset($items->entries[$count]) ? $items->entires[$count] : false;
            if ($t === false){
                $notValid = true;
            }

            $exists = $this->doctrine->getRepository('AppBundle:JournalTransaction')
                ->hasTransaction($acc, $t->refID, $t->amount);

            if ($exists === null){
                $jTran = $this->mapItem($t);
                $acc->addJournalTransaction($jTran);

            } else  {
                if ($exists->getRefType() === null){
                    $refType = $this->doctrine->getRepository('AppBundle:RefType')
                        ->findOneBy(['ref_type_id' => $exists->getRefTypeId()]);

                    if ($refType instanceof RefType){
                        $exists->setRefType($refType);
                        $em->persist($exists);
                    }
                }

                $notValid = true;
            }

            $count++;
        }

        $this->log->info(sprintf("Done in %s", $count));

        $em->persist($acc);
    }

    public function mapItem($item){
        $jTran = new JournalTransaction();

        $refType = $this->doctrine->getRepository('AppBundle:RefType')
            ->findOneBy(['ref_type_id' => $item->refTypeID]);

        if ($refType instanceof RefType){
            $jTran->setRefType($refType);
        }

        $jTran->setDate(new \DateTime($item->date))
            ->setRefId($item->refID)
            ->setRefTypeId($item->refTypeID)
            ->setOwnerName1($item->ownerName1)
            ->setOwnerId1($item->ownerID1)
            ->setOwnerName2($item->ownerName2)
            ->setOwnerId2($item->ownerID2)
            ->setArgName1($item->argName1)
            ->setArgId1($item->argID1)
            ->setAmount($item->amount)
            ->setBalance($item->balance)
            ->setReason($item->reason)
            ->setOwner1TypeId($item->owner1TypeID)
            ->setOwner2TypeId($item->owner2TypeID);

        return $jTran;
    }



    public static function getName(){
        return 'journal_transaction_manager';
    }
}
