<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Exception\InvalidExpirationException;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AccountManager {

    private $pheal;

    private $doctrine;

    public function __construct(PhealFactory $pheal, Registry $doctrine){
        $this->pheal = $pheal;
        $this->doctrine = $doctrine;
    }

    public function validateAndUpdateApiKey(ApiCredentials $entity){
        $client = $this->getClient($entity);

        $result = $client->APIKeyInfo();
        $key = $result->key;

        list($type, $expires, $accessMask) = [ $key->type, $key->expires, $key->accessMask ];

        if (strlen($expires) > 0) {
            throw new InvalidExpirationException('Expiration Date on API Key is finite.');
        }

        $char = $result->key
            ->characters[0]
            ->characterID;

        $corp = $result->key
            ->characters[0]
            ->corporationID;


        $entity->setAccessMask($accessMask)
            ->setType($type)
            ->setCharacterId($char)
            ->setCorporationId($corp);

    }

    public function updateLatestBalances(array $accounts){
        $balanceRepo = $this->doctrine->getRepository('AppBundle:AccountBalance');
        foreach($accounts as $acc){
            $balance = $balanceRepo->getLatestBalance($acc)
                ->getBalance();

            $lastDay = ($b = $balanceRepo->getLastDayBalance($acc)) instanceof AccountBalance
                ? $b->getBalance()
                : 0;

            $acc->setCurrentBalance($balance)
                ->setLastDayBalance($lastDay);
        }
    }


    protected function getClient(ApiCredentials $entity){
        return $this->pheal->createEveOnline($entity->getApiKey(), $entity->getVerificationCode());
    }
}
