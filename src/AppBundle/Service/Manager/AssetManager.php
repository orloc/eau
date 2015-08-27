<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Corporation;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AssetManager
{

    private $pheal;

    public function __construct(PhealFactory $pheal)
    {
        $this->pheal = $pheal;
    }

    public function getAssetList(Corporation $corporation){
        $client = $this->getClient($corporation);

        $result = $client->AssetList();

        $list = $result->assets;

        foreach ($list as $item) {

        }


    }

    private function getClient(Corporation $corporation, $scope = 'corp'){

        $key = $corporation->getApiCredentials();
        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
