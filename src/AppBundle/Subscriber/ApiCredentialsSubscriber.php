<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Service\Manager\ApiKeyManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiCredentialsSubscriber implements EventSubscriber {

    private $tokenManager;

    public function __construct(TokenStorageInterface $storage){
        $this->tokenManager = $storage;
    }

    public function getSubscribedEvents(){
        return [
            'prePersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getObject();

        if ($entity instanceof ApiCredentials){
            $user = $this->tokenManager->getToken()->getUser();
            $entity->setCreatedBy($user);
        }
    }


}
