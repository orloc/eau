<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\ParameterBag;

class CharacterManager {

    private $log;

    public function __construct(Logger $logger){
        $this->log = $logger;
    }

    public function createCharacter(array $details){
        $char = new Character();

        $char->setEveId($details['characterID'])
            ->setName($details['characterName'])
            ->setEveCorporationId($details['corporationID']);

        return $char;
    }


    public function newCharacterWithName(array $details){

        $char = new Character();

        $char->setEveId($details['id'])
            ->setName($details['name']);

        return $char;

    }


}
