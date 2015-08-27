<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\Corporation;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AssetManager
{

    private $pheal;

    public function __construct(PhealFactory $pheal)
    {
        $this->pheal = $pheal;
    }

    public function generateAssetList(Corporation $corporation){
        $client = $this->getClient($corporation);

        $result = $client->AssetList();

        $list = $result->assets;

        $grouping = new AssetGroup();
        foreach ($list as $i) {

            $item = $this->mapAsset($i);

            if (isset($i->contents) && count($i->contents)){
                foreach ($i->contents as $innerI){
                    $it = $this->mapAsset($innerI);
                    if (isset($innerI->contents) && count($innerI->contents)){
                        foreach ($innerI->contents as $innerII) {
                            $iit = $this->mapAsset($innerII);

                            $it->addContent($iit);
                        }
                    }
                    $item->addContent($it);
                }
            }

            $grouping->addAsset($item);

        }

        $corporation->addAssetGroup($grouping);

    }

    private function mapAsset($i){
        $item = new Asset();

        $item->setFlag($i->flag)
            ->setItemId($i->itemID)
            ->setQuantity($i->quantity)
            ->setSingleton($i->singleton)
            ->setTypeId($i->typeID);

        if (isset($i->locationID)){
            $item->setLocationId($i->locationID);
        }
        return $item;
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
