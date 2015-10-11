<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\Character;
use AppBundle\Service\Manager\CharacterManager;
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

        if ($entity instanceof Character){
            $name = $this->session->get('registration_authorized');
            $newChar = $this->manager
                ->newCharacterWithName($name);

            var_dump($newChar);
            die;
        }
    }
}