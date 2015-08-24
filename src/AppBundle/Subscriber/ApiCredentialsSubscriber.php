<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Exception\DuplicateResourceException;
use AppBundle\Service\Manager\AccountManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiCredentialsSubscriber implements EventSubscriber {

    private $tokenManager;
    private $manager;

    public function __construct(TokenStorageInterface $storage, AccountManager $manager){
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

        if ($entity instanceof ApiCredentials && $entity->getId() === null){
            $user = $this->tokenManager->getToken()->getUser();
            $entity->setCreatedBy($user);

            $this->manager->validateAndUpdateApiKey($entity);

            if (($corpId = $entity->getCorporationId()) !== null){
                $exists = $em->getRepository('AppBundle:Corporation')->findOneBy(['eve_id' => $corpId]);

                if ($exists instanceof Corporation){
                    throw new DuplicateResourceException(sprintf("Corporation %s already exists in the database", $exists->getName()));
                }
            }
        }
    }


}
