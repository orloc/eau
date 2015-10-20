<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\ParameterBag;

class CharacterManager {

    private $api_manager;
    private $log;

    public function __construct(Logger $logger, ApiKeyManager $apiManager){
        $this->log = $logger;
        $this->api_manager = $apiManager;
    }

    public function buildInstanceFromRequest(ParameterBag $content){
        $corp = new Character();

        $creds = $this->api_manager->buildInstanceFromRequest($content);
        $creds->setIsActive(true);

        $corp->addApiCredential($creds);

        return $corp;
    }

    public function newCharacterWithName(array $details){

        $char = new Character();

        $char->setEveId($details['id'])
            ->setName($details['name']);

        return $char;

    }


}
