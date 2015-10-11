<?php

namespace AppBundle\Service\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\ParameterBag;

class CharacterManager {

    private $log;

    public function __construct(Logger $logger){
        $this->log = $logger;
    }

    public function newCharacterWithName($name){

    }


}
