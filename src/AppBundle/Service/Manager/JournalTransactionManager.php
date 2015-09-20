<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Account;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\JournalTransaction;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use EveBundle\Repository\Registry as EveRegistry;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class JournalTransactionManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    protected $pheal;

    protected $log;

    public function __construct(PhealFactory $pheal, EveRegistry $registry, Registry $doctrine, Logger $log)
    {
        parent::__construct($doctrine, $registry);
        $this->pheal = $pheal;
        $this->log = $log;
    }

    public function updateJournalTransactions(Corporation $corporation, $fromID = null){
        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($corporation);

        if ($apiKey === null){
            throw new \Exception('No active api key for corp' . $corporation->getId() .' found');

        }

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

        if (!$corp instanceof Corporation || !$acc instanceof Account) {
            throw new OptionDefinitionException(sprintf('Option corp required and must by of type %s', get_class(new Corporation())));
        }

        foreach ($items->entries as $t){
            $this->log->debug("processing {$t->refID}");
            $exists = $this->doctrine->getRepository('AppBundle:JournalTransaction')
                ->hasTransaction($acc, $t->refID, $t->amount);

            if ($exists === null){
                $jTran = $this->mapItem($t);
                $acc->addJournalTransaction($jTran);

            } else  {
                $this->log->info(sprintf("Conflicting Journal Ref %s for %s %s", $t->refID, $acc->getDivision(), $corp->getName()));
            }
        }
    }

    public function mapItem($item){
        $jTran = new JournalTransaction();
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


    public function getClient(ApiCredentials $key, $scope = 'corp'){

        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
