<?php

namespace AppBundle\EventListener\Corporation;


use AppBundle\Event\CorporationEvents;
use AppBundle\Event\NewCorporationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewCorporationListener implements EventSubscriberInterface {

    public static function getSubscribedEvents(){
        return [ CorporationEvents::NEW_CORPORATION =>  'updateDetails' ];
    }

    public function updateDetails(NewCorporationEvent $event){

        var_dump($event->getCorporation());die;


    }
}