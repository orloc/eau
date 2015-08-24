<?php

namespace AppBundle\EventListener\Corporation;


use AppBundle\Event\CorporationEvents;
use AppBundle\Event\NewCorporationEvent;
use AppBundle\Service\Manager\CorporationManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewCorporationListener implements EventSubscriberInterface {

    protected $manager;

    protected $em;

    public function __construct(CorporationManager $manager, EntityManager $em){
        $this->em = $em;
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(){
        return [ CorporationEvents::NEW_CORPORATION =>  'updateDetails' ];
    }

    public function updateDetails(NewCorporationEvent $event){
        $corporation = $event->getCorporation();

        $result = $this->manager->getCorporationDetails($corporation);

        $corporation->setName($result['name'])
            ->setEveId($result['id']);

        $this->manager->generateAccounts($corporation);

        $this->manager->generateTransactions($corporation);

        $this->em->persist($corporation);
        $this->em->flush();

    }
}