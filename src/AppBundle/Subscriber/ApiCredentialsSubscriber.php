<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Service\Manager\ApiKeyManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiCredentialsSubscriber implements EventSubscriber {

    private $tokenManager;
    private $manager;

    public function __construct(TokenStorageInterface $storage, ApiKeyManager $manager){
        $this->tokenManager = $storage;
        $this->manager = $manager;
    }

    public function getSubscribedEvents(){
        return [
            'prePersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getObject();
        $em = $args->getObjectManager();

        if ($entity instanceof ApiCredentials && $entity->getId() === null && $entity->getType() === 'Corporation'){
            $result = $this->manager->validateAndUpdateApiKey($entity);
        }

        if ($entity instanceof ApiCredentials){
            $user = $this->tokenManager->getToken()->getUser();
            $entity->setCreatedBy($user);
        }
    }


}
