<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Exception\InvalidExpirationException;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class ApiCredentialsSubscriber implements EventSubscriber {

    private $tokenManager;
    private $pheal;

    public function __construct(TokenStorageInterface $storage, PhealFactory $pheal){
        $this->pheal = $pheal;
        $this->tokenManager = $storage;
    }

    public function getSubscribedEvents(){
        return [
            'prePersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getObject();

        if ($entity instanceof ApiCredentials && $entity->getId() === null){
            $user = $this->tokenManager->getToken()->getUser();
            $entity->setCreatedBy($user);

            $this->updateApiData($entity);
        }
    }

    protected function updateApiData(ApiCredentials $entity){
        $client = $this->pheal->createEveOnline($entity->getApiKey(), $entity->getVerificationCode());
        // validate API MASK
        $result = $client->APIKeyInfo()->key;
        list($type, $expires, $accessMask) = [ $result->type, $result->expires, $result->accessMask ];


        if (strlen($expires) > 0) {
            throw new InvalidExpirationException('Expiration Date on API Key is finite.');
        }

        $entity->setAccessMask($accessMask)
            ->setType($type);

    }

}
