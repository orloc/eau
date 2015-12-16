<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Corporation;
use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CorporationSubscriber implements EventSubscriber {

    private $tokenManager;

    public function __construct(TokenStorageInterface $storage){
        $this->tokenManager = $storage;
    }

    public function getSubscribedEvents(){
        return [
            'prePersist'
        ];
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getObject();

        if ($entity instanceof Corporation && php_sapi_name() !== 'cli'){
            $user = $this->tokenManager->getToken()->getUser();

            if (!$user instanceof User){
                throw new AccessDeniedException('Unauthorized User');
            }
            $entity->setCreatedBy($user);
        }
    }
}