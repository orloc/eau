<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\ApiCredentials;
use AppBundle\Exception\InvalidExpirationException;
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

        $entity->setAccessMask($accessMask)
            ->setType($type)
            ->setCharacterId($char);

    }

    public function getAccountStatus(){
        $client = $this->pheal->createEveOnline(4624909, '67FGTUIkjVEAQSgNTTHP9F6k3tdoCNEasrujfISp2RJL63bJC9yC6ha0HiobypPr');

        var_dump($client->accountStatus()->logonMinutes / 60 / 24);die;
        var_dump($result);die;
    }

    protected function getClient(ApiCredentials $entity){
        return $this->pheal->createEveOnline($entity->getApiKey(), $entity->getVerificationCode());
    }
}
