<?php

namespace AppBundle\Event;

use AppBundle\Entity\Corporation;
use Symfony\Component\EventDispatcher\Event;

class NewCorporationEvent extends Event {

    protected $corporation;

    public function __construct(Corporation $corp){
        $this->corporation = $corp;
    }

    public function getCorporation(){
        return $this->corporation;
    }

}