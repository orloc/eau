<?php

namespace AppBundle\Service\DataManager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Exception\InvalidAccessMaskException;
use AppBundle\Exception\InvalidApiKeyTypeException;
use AppBundle\Exception\InvalidExpirationException;
use Symfony\Component\HttpFoundation\ParameterBag;

class ApiKeyManager extends AbstractManager implements DataManagerInterface {

    public function validateKey(ApiCredentials $entity, $required_type = false, $required_mask = false){
        $key = $this->getKeyInfo($entity);
        list($type, $expires, $accessMask) = [$key->type, $key->expires, $key->accessMask];

        if (strlen($expires) > 0) {
            throw new InvalidExpirationException('Expiration Date on API Key is finite.');
        }

        // char or corp
        if ($accessMask !== '1073741823' && $accessMask !== '134217727'){
            throw new InvalidAccessMaskException('Your Access Mask is invalid - please use the link above to generate a valid key');
        }

        if ($required_type && $type !== $required_type){
            throw new InvalidApiKeyTypeException('Api Key must be of type:'.$required_type.' - '.$type.' given');
        }

        $exists = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->findOneBy(['api_key' => $entity->getApiKey(), 'verification_code' => $entity->getVerificationCode()]);

        if ($exists instanceof ApiCredentials){
            throw new \Exception('API key already exists');
        }

        return $key;
    }

    public function updateCorporationKey(ApiCredentials $key, $result){
        var_dump(get_class($result));
        $result_key = $result->toArray()['result']['key'];
        $character = array_pop($result_key['characters']);

        $key->setEveCharacterId($character['characterID'])
            ->setEveCorporationId($character['corporationID']);

        return $key;
    }

    public function validateAndUpdateApiKey(ApiCredentials $entity, $required_type = false) {
        $key = $this->validateKey($entity, $required_type, null);

        $entity->setAccessMask($key->accessMask)
            ->setType($key->type)
            ->setIsActive(true);

        return $key;
    }

    public function updateKey(ApiCredentials $key, array $creds){
        $key->setType($creds['type'])
            ->setAccessMask($creds['accessMask'])
            ->setIsActive(true);
    }

    public function buildInstanceFromRequest(ParameterBag $content){
        $creds = new ApiCredentials();

        $creds->setVerificationCode($content->get('verification_code'))
            ->setApiKey($content->get('api_key'));

        return $creds;
    }

    public static function getName(){
        return 'api_key_manager';
    }

    protected function getKeyInfo(ApiCredentials $entity){
        $client = $this->getClient($entity, 'account');
        $result = $client->APIKeyInfo();

        return $result->key;
    }


}
