<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\ApiCredentials;
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

        if ($entity instanceof ApiCredentials && php_sapi_name() !== 'cli'){
            $user = $this->tokenManager->getToken()->getUser();
            $entity->setCreatedBy($user);
        }
    }


}
