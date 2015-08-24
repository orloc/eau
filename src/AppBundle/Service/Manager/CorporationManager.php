<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\Account;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\JournalTransaction;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class CorporationManager {

    private $pheal;

    public function __construct(PhealFactory $pheal){
        $this->pheal = $pheal;
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

    public function generateAccounts(Corporation $corporation){
        $client = $this->getClient($corporation);

        $accounts = $client->AccountBalance()->accounts;

        foreach ($accounts as $a){
            $account = new Account();

            $account->setEveAccountId($a->accountID)
                ->setDivision($a->accountKey);

            $balance = new AccountBalance();
            $balance->setBalance($a->balance);

            $account->addBalance($balance);

            $corporation->addAccount($account);
        }
    }

    public function generateJournalTransactions(Corporation $corporation){
        $client = $this->getClient($corporation);

        $accounts = $corporation->getAccounts();
        $key = $corporation->getApiCredentials();

        foreach($accounts as $acc){
            $transactions = $client->WalletJournal([
                'accountKey' => $acc->getDivision(),
                'rowCount' => 2000
            ]);

            foreach ($transactions->entries as $t){
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
                    ->setOwner1TypeID($t->owner1TypeID)
                    ->setOwner2TypeID($t->owner2TypeID);

                var_dump($t);
                die;
            }
        }

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