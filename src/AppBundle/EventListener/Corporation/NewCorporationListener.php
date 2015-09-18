<?php

namespace AppBundle\EventListener\Corporation;


use AppBundle\Entity\ApiUpdate;
use AppBundle\Event\CorporationEvents;
use AppBundle\Event\NewCorporationEvent;
use AppBundle\Service\Manager\ApiKeyManager;
use AppBundle\Service\Manager\CorporationManager;
use AppBundle\Service\Manager\AssetManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewCorporationListener implements EventSubscriberInterface {

    protected $manager;

    protected $asset_manager;

    protected $api_manager;

    protected $em;

    public function __construct(CorporationManager $manager, AssetManager $aManager, ApiKeyManager $apiManager, EntityManager $em){
        $this->em = $em;
        $this->api_manager = $apiManager;
        $this->manager = $manager;
        $this->asset_manager = $aManager;
    }

    public static function getSubscribedEvents(){
        return [ CorporationEvents::NEW_CORPORATION =>  'updateDetails' ];
    }

    public function updateDetails(NewCorporationEvent $event){
        $corporation = $event->getCorporation();

        $result = $this->manager->getCorporationDetails($corporation);

        $corporation->setName($result['name'])
            ->setEveId($result['id']);

        // i dont like this but i want to save data so that if other things fail this sticks
        $this->manager->updateAccounts($corporation);

        $this->em->persist($corporation);
        $this->em->flush();

        $this->manager->updateJournalTransactions($corporation);
        $this->manager->updateMarketTransactions($corporation);

        $corporation->addApiUpdate(
            $this->createAccess(ApiUpdate::CACHE_STYLE_SHORT)
        );

        $this->em->persist($corporation);
        $this->em->flush();

        $this->asset_manager->generateAssetList($corporation);

        $corporation->addApiUpdate(
            $this->createAccess(ApiUpdate::CACHE_STYLE_LONG)
        );

        $this->em->persist($corporation);
        $this->em->flush();

    }

    protected function createAccess($type){
        $access = new ApiUpdate();

        $access->setType($type);

        return $access;
    }
}