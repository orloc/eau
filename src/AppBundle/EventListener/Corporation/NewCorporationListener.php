<?php

namespace AppBundle\EventListener\Corporation;


use AppBundle\Event\CorporationEvents;
use AppBundle\Event\NewCorporationEvent;
use AppBundle\Service\EveDataUpdateService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewCorporationListener implements EventSubscriberInterface {

    protected $manager;

    protected $em;

    public function __construct(EveDataUpdateService $updateService, EntityManager $em){
        $this->em = $em;
        $this->manager = $updateService;
    }

    public static function getSubscribedEvents(){
        return [ CorporationEvents::NEW_CORPORATION =>  'updateDetails' ];
    }

    public function updateDetails(NewCorporationEvent $event){
        $corporation = $event->getCorporation();

        $this->manager->checkCorporationDetails($corporation);

        $this->manager->updateShortTimerCalls($corporation);
        $this->manager->updateLongTimerCalls($corporation);


    }
}