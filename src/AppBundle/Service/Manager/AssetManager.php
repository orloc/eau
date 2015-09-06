<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\Corporation;
use AppBundle\Service\EBSDataMapper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AssetManager
{

    private $pheal;

    private $doctrine;

    private $mapper;

    public function __construct(PhealFactory $pheal, Registry $doctrine, EBSDataMapper $dataMapper)
    {
        $this->pheal = $pheal;
        $this->doctrine = $doctrine;
        $this->mapper = $dataMapper;
    }

    public function generateAssetList(Corporation $corporation){
        $client = $this->getClient($corporation);

        $result = $client->AssetList();

        $list = $result->assets;
        $grouping = new AssetGroup();
        $this->mapList($list, $grouping);
        $corporation->addAssetGroup($grouping);

    }

    public function updateResultSet($items){
        $itemTypes = $this->doctrine->getRepository('EveBundle:ItemType', 'eve_data');

        foreach ($items as $i){
            $updateData = $itemTypes->getItemTypeData($i->getTypeId());
            $this->mapper->updateObject($i, $updateData);
        }

        return $items;
    }


    private function mapList($assets, AssetGroup $grouping, Asset $parent = null){
        foreach ($assets as $asset){
            $newAsset = $this->mapAsset($asset);
            $grouping->addAsset($newAsset);

            if ($parent) {
                $parent->addContent($newAsset);
            }

            if (isset($asset->contents)){
                $this->mapList($asset->contents, $grouping, $newAsset) ;
            }
        }
    }

    private function mapAsset($i){
        $item = new Asset();

        $item->setFlagId($i->flag)
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
