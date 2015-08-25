<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\Account;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\JournalTransaction;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class CorporationManager {

    private $pheal;
    private $registry;
    private $log;

    public function __construct(PhealFactory $pheal, Registry $registry, Logger $logger){
        $this->pheal = $pheal;
        $this->registry = $registry;
        $this->log = $logger;
    }

    public function buildInstanceFromRequest(ParameterBag $content){
        $corp = new Corporation();
        $creds = new ApiCredentials();

        $creds->setVerificationCode($content->get('verification_code'))
            ->setApiKey($content->get('api_key'));

        $corp->setApiCredentials($creds);

        return $corp;
    }

    public function getCorporationDetails(Corporation $entity){
        $client = $this->getClient($entity, 'account');

        $details = $client->APIKeyInfo()->key->characters[0];
        $result =  [ 'name' => $details->corporationName , 'id' => $details->corporationID ];

        return $result;
    }

    public function updateAccounts(Corporation $corporation){
        $client = $this->getClient($corporation);

        $accounts = $client->AccountBalance()->accounts;
        $repo = $this->registry->getRepository('AppBundle:Account');

        foreach ($accounts as $a){
            $exists = $repo->findOneBy([
                'corporation' => $corporation,
                'division' => $a->accountKey
            ]);

            if (!$exists instanceof Account){
                $account = new Account();
                $account->setEveAccountId($a->accountID)
                    ->setDivision($a->accountKey);
            } else {
                $account = $exists;
            }

            $balance = new AccountBalance();
            $balance->setBalance($a->balance);

            $account->addBalance($balance);

            if (!$exists instanceof Account){
                $corporation->addAccount($account);
            }
        }
    }

    public function updateJournalTransactions(Corporation $corporation, $fromID = null){
        $client = $this->getClient($corporation);

        $accounts = $corporation->getAccounts();

        // used for comparison
        $now = new \DateTime();
        $now->sub(new \DateInterval('P2D'));

        foreach($accounts as $acc){
            $this->log->debug(sprintf("Processing account %s for %s", $acc->getDivision(), $corporation->getName()));
            $params =  [
                'accountKey' => $acc->getDivision(),
                'rowCount' => 2000
            ];

            if ($fromID){
                $params = array_merge($params, [ 'fromID' => $fromID]);
            }

            $transactions = $client->WalletJournal($params);

            foreach ($transactions->entries as $t){
                $this->log->debug("processing {$t->refID}");
                $exists = $this->registry->getRepository('AppBundle:JournalTransaction')
                    ->hasTransaction($acc, $t->refID);

                if ($exists === null){
                    $this->log->debug(sprintf('No exisiting transaction found for %s  in %s @ %s', $t->refID, $acc->getDivision(), $corporation->getName()));
                    $jTran = new JournalTransaction();
                    $jTran->setDate(new \DateTime($t->date))
                        ->setRefId($t->refID)
                        ->setRefTypeId($t->refTypeID)
                        ->setOwnerName1($t->ownerName1)
                        ->setOwnerId1($t->ownerID1)
                        ->setOwnerName2($t->ownerName2)
                        ->setOwnerId2($t->ownerID2)
                        ->setArgName1($t->argName1)
                        ->setArgId1($t->argID1)
                        ->setAmount($t->amount)
                        ->setBalance($t->balance)
                        ->setReason($t->reason)
                        ->setOwner1TypeId($t->owner1TypeID)
                        ->setOwner2TypeId($t->owner2TypeID);

                    $acc->addJournalTransaction($jTran);
                } else  {
                    $this->log->warning(sprintf("Conflicting Journal Ref %s for %s %s", $t->refID, $acc->getDivision(), $corporation->getName()));
                }
            }
        }
    }

    public function updateMarketTransactions(Corporation $corporation, $fromID = null){
        $client = $this->getClient($corporation);

        $accounts = $corporation->getAccounts();

        // used for comparison
        $now = new \DateTime();
        $now->sub(new \DateInterval('P2D'));

        foreach($accounts as $acc){
            $this->log->debug(sprintf("Processing account %s for %s", $acc->getDivision(), $corporation->getName()));
            $params =  [
                'accountKey' => $acc->getDivision(),
                'rowCount' => 2000
            ];

            if ($fromID){
                $params = array_merge($params, [ 'fromID' => $fromID]);
            }

            $transactions = $client->WalletTransactions($params);

            foreach ($transactions->entries as $t){
                $this->log->debug("processing {$t->refID}");
                $exists = $this->registry->getRepository('AppBundle:MarketTransaction')
                    ->hasTransaction($acc, $t->refID);

                if ($exists === null){
                    $this->log->debug(sprintf('No exisiting transaction found for %s  in %s @ %s', $t->refID, $acc->getDivision(), $corporation->getName()));

                    var_dump($t);

                } else  {
                    $this->log->warning(sprintf("Conflicting Journal Ref %s for %s %s", $t->refID, $acc->getDivision(), $corporation->getName()));
                }
            }
        }
        die;

    }

    private function getClient(Corporation $corporation, $scope = 'corp'){

        $key = $corporation->getApiCredentials();
        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}