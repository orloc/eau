<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use AppBundle\Service\DataManager\CharacterManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserSubscriber implements EventSubscriber {

    private $manager;
    private $session;

    public function __construct(CharacterManager $manager, Session $session){
        $this->manager = $manager;
        $this->session = $session;
    }

    public function getSubscribedEvents(){
        return [
            'prePersist'
        ];
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getObject();

        if ($entity instanceof User){
            if ($this->session->has('registration_authorized')){
                $this->session->remove('registration_authorized');
            }
        }
    }
}