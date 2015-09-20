<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class CorporationManager implements DataManagerInterface {

    private $pheal;
    private $doctrine;
    private $log;

    public function __construct(PhealFactory $pheal, Registry $registry, Logger $logger){
        $this->pheal = $pheal;
        $this->doctrine = $registry;
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
        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($entity);

        if ($apiKey === null){
            throw new \Exception('No active api key for corp' . $entity->getId() .' found');

        }

        $client = $this->getClient($apiKey, 'account');

        $details = $client->APIKeyInfo()->key->characters[0];
        $result =  [ 'name' => $details->corporationName , 'id' => $details->corporationID ];

        return $result;
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