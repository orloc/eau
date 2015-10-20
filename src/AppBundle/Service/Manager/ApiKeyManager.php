<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Exception\InvalidExpirationException;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class ApiKeyManager implements DataManagerInterface {

    private $pheal;

    private $doctrine;

    public function __construct(PhealFactory $pheal, Registry $doctrine) {
        $this->pheal = $pheal;
        $this->doctrine = $doctrine;
    }

    public function validateAndUpdateApiKey(ApiCredentials $entity) {
        $client = $this->getClient($entity, 'account');

        $result = $client->APIKeyInfo();

        $key = $result->key;

        list($type, $expires, $accessMask) = [$key->type, $key->expires, $key->accessMask];

        if (strlen($expires) > 0) {
            throw new InvalidExpirationException('Expiration Date on API Key is finite.');
        }

        $char = $result->key
            ->characters[0]
            ->characterID;

        var_dump($type);die;
        $corp = $result->key
            ->characters[0]
            ->corporationID;


        $entity->setAccessMask($accessMask)
            ->setType($type)
            ->setEveCharacterId($char)
            ->setEveCorporationId($corp);

    }

    public function updateActiveKey(Corporation $corporation, ApiCredentials $key){

    }

    public function buildInstanceFromRequest(ParameterBag $content){
        $creds = new ApiCredentials();

        $creds->setVerificationCode($content->get('verification_code'))
            ->setApiKey($content->get('api_key'));

        return $creds;
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
