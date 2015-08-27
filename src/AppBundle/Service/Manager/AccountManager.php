<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\ApiCredentials;
use AppBundle\Exception\InvalidExpirationException;
use Doctrine\ORM\EntityManager;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AccountManager {

    private $pheal;

    public function __construct(PhealFactory $pheal){
        $this->pheal = $pheal;
    }

    public function validateAndUpdateApiKey(ApiCredentials $entity){
        $client = $this->getClient($entity);

        $result = $client->APIKeyInfo();
        $key = $result->key;

        list($type, $expires, $accessMask) = [ $key->type, $key->expires, $key->accessMask ];

        if (strlen($expires) > 0) {
            throw new InvalidExpirationException('Expiration Date on API Key is finite.');
        }

        $char = $result->key
            ->characters[0]
            ->characterID;

        $corp = $result->key
            ->characters[0]
            ->corporationID;


        $entity->setAccessMask($accessMask)
            ->setType($type)
            ->setCharacterId($char)
            ->setCorporationId($corp);

    }

    protected function getClient(ApiCredentials $entity){
        return $this->pheal->createEveOnline($entity->getApiKey(), $entity->getVerificationCode());
    }
}
