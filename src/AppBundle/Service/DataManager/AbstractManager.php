<?php

namespace AppBundle\Service\DataManager;

use AppBundle\Entity\Account;
use AppBundle\Entity\Corporation;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Entity\ApiCredentials;
use EveBundle\Repository\Registry as EveRegistry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

abstract class AbstractManager {

    protected $doctrine;

    protected $registry;

    protected $pheal;

    public function __construct(PhealFactory $pheal, Registry $doctrine, EveRegistry $registry){
        $this->pheal = $pheal;
        $this->doctrine = $doctrine;
        $this->registry = $registry;
    }

    public function buildTransactionParams(Account $acc, $fromID = null){
        $params =  [
            'accountKey' => $acc->getDivision(),
            'rowCount' => 2000
        ];

        if ($fromID){
            $params = array_merge($params, [ 'fromID' => $fromID]);
        }

        return $params;
    }

    public function getApiKey(Corporation $entity){
        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($entity);

        if ($apiKey === null){
            throw new \Exception('No active api key for corp' . $entity->getId() .' found');
        }

        return $apiKey;
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