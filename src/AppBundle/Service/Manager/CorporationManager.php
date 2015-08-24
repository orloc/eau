<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\Account;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
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

    public function updateMarketOrders(Corporation $corporation){
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