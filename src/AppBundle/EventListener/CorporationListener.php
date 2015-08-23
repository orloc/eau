<?php

namespace AppBundle\EventListener;


use AppBundle\Entity\Corporation;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class CorporationListener implements EventSubscriber {

    private $tokenManager;
    private $pheal;

    public function __construct(TokenStorageInterface $storage, PhealFactory $pheal){
        $this->pheal = $pheal;
        $this->tokenManager = $storage;
    }

    public function getSubscribedEvents(){
        return [
            'prePersist'
        ];
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getObject();

        if ($entity instanceof Corporation){
            $user = $this->tokenManager->getToken()->getUser();
            $entity->setCreatedBy($user);

            $this->updateCorporationData($entity);



        }
    }

    protected function updateCorporationData(Corporation $entity){

        $client = $this->pheal->createEveOnline($entity->getApiKey(), $entity->getVerificationCode());
        // validate API MASK
        $result = $client->APIKeyInfo();;

        $character = $result->key->characters;


        $entity->setName($result->key->characters->toArray()[0]['corporationName']);
        var_dump($entity, $type);die;

    }

}