<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Exception\InvalidExpirationException;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class ApiKeyManager {
    private $pheal;

    private $doctrine;

    public function __construct(PhealFactory $pheal, Registry $doctrine) {
        $this->pheal = $pheal;
        $this->doctrine = $doctrine;
    }

    public function validateAndUpdateApiKey(ApiCredentials $entity) {
        $client = $this->getClient($entity);

        $result = $client->APIKeyInfo();
        $key = $result->key;

        list($type, $expires, $accessMask) = [$key->type, $key->expires, $key->accessMask];

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

    public function getClient(ApiCredentials $credentials, $scope = 'corp'){
        $client = $this->pheal->createEveOnline(
            $credentials->getApiKey(),
            $credentials->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
