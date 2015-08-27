<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGrouping;
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

        $grouping = new AssetGrouping();
        foreach ($list as $i) {

            $item = new Asset();

            $item->setFlag($i->flag)
                ->setItemId($i->itemID)
                ->setLocationId($i->locationID)
                ->setQuantity($i->quantity)
                ->setSingleton($i->singleton)
                ->setRawQuantity($i->rawQuantity)
                ->setTypeId($i->typeID);

            var_dump($item);

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
