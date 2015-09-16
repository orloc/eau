<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\Corporation;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Doctrine\ORM\EntityManager;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class MarketOrderManager {

    private $pheal;

    private $mapper;

    private $eve_registry;

    private $doctrine;

    public function __construct(PhealFactory $pheal, EBSDataMapper $dataMapper, EveRegistry $registry, Registry $doctrine){
        $this->pheal = $pheal;
        $this->mapper = $dataMapper;
        $this->eve_registry = $registry;
        $this->doctrine = $doctrine;
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
