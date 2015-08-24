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

    public function finalizeApiKeyUpdate(ApiCredentials $entity){
        $client = $this->getClient($entity);

        $result = $client->APIKeyInfo()->character;

        return $result;
    }

    public function validateAndUpdateApiKey(ApiCredentials $entity){
        $client = $this->getClient($entity);

        $result = $client->APIKeyInfo()->key;
        list($type, $expires, $accessMask) = [ $result->type, $result->expires, $result->accessMask ];

        if (strlen($expires) > 0) {
            throw new InvalidExpirationException('Expiration Date on API Key is finite.');
        }

        $entity->setAccessMask($accessMask)
            ->setType($type);

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
