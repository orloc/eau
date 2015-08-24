<?php

namespace AppBundle\EventListener\Corporation;


use AppBundle\Event\CorporationEvents;
use AppBundle\Event\NewCorporationEvent;
use AppBundle\Service\Manager\AccountManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewCorporationListener implements EventSubscriberInterface {

    protected $manager;

    public function __construct(AccountManager $manager){
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(){
        return [ CorporationEvents::NEW_CORPORATION =>  'updateDetails' ];
    }

    public function updateDetails(NewCorporationEvent $event){
        $corporation = $event->getCorporation();

        $result = $this->manager->finalizeApiKeyUpdate($corporation->getApiCredentials());
        $charDetails = $result->character;

        $corporation->setName($charDetails->corporationName)
            ->setEveId($charDetails->corporationID);


    }
}