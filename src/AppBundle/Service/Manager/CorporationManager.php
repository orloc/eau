<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use Symfony\Component\HttpFoundation\ParameterBag;

class CorporationManager {

    public function buildInstanceFromRequest(ParameterBag $content){
        $corp = new Corporation();
        $creds = new ApiCredentials();

        $creds->setVerificationCode($content->get('verification_code'))
            ->setApiKey($content->get('api_key'));

        $corp->setApiCredentials($creds);

        return $corp;
    }
}