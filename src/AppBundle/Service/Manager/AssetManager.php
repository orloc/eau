<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\Corporation;
use AppBundle\Service\EBSDataMapper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EveBundle\Entity\AveragePrice;
use \EveBundle\Repository\Registry as EveRegistry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AssetManager
{

    private $pheal;

    private $registry;

    private $mapper;

    private $doctrine;

    public function __construct(PhealFactory $pheal, EBSDataMapper $dataMapper, EveRegistry $registry, Registry $doctrine)
    {
        $this->pheal = $pheal;
        $this->mapper = $dataMapper;
        $this->doctrine = $doctrine;
        $this->registry = $registry;
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
        $itemTypes = $this->registry->get('EveBundle:ItemType');
        $regions = $this->registry->get('EveBundle:Region');
        $constellations = $this->registry->get('EveBundle:Constellation');
        $solarsystems = $this->registry->get('EveBundle:SolarSystem');
        $locations = $this->registry->get('EveBundle:StaStations');

        foreach ($items as $i){
            $locationData = $locations->getLocationInfo($i->getLocationId());

            $updateData = array_merge(
                $itemTypes->getItemTypeData($i->getTypeId()),
                is_array(($ss = $solarsystems->getSolarSystemById($locationData['solar_system']))) ? $ss : [],
                is_array(($con = $constellations->getConstellationById($locationData['constellation'])))? $con: [],
                is_array(($reg = $regions->getRegionById($locationData['region']))) ? $reg : [],
                ['station' => $locationData['station_name']]
            );

            $i->setDescriptors($updateData);
        }

        return $items;
    }

    public function updatePrices(array $items){
        $prices = $this->doctrine->getManager('eve_data')
            ->getRepository('EveBundle:AveragePrice');

        $types = [];
        foreach ($items as $i){
            $descriptors = $i->getDescriptors();

            if (!isset($types[$i->getTypeId()])){
                $price = $prices->getAveragePriceByType($i->getTypeId());
                $types[$i->getTypeId()] = $descriptors['price'] = $price instanceof AveragePrice
                    ? floatval($price->getAveragePrice())
                    : 0;

                $descriptors['total_price'] = floatval($descriptors['price'] * $i->getQuantity());

            } else {
                $descriptors['price'] = floatval($types[$i->getTypeId()]);
                $descriptors['total_price'] = floatval($descriptors['price']) * $i->getQuantity();
            }

            $i->setDescriptors($descriptors);
        }
    }

    public function findTopLevelPriceTotals(array $list ){
        $flattened = $this->flattenArray($list);

        $price = array_reduce($flattened, function ($carry, $data){
            if ($carry === null){
                return $data->getDescriptors()['total_price'];
            }

           return $data->getDescriptors()['total_price'] + $carry;
        });

        return $price;

    }

    private function flattenArray(array $arr){
        $return = [];
        foreach ($arr as $key => $val){
            if (is_array($val)) {
                $return = array_merge($return, $this->flattenArray($val));
            } else {
                $return[$key] = $val;
            }
        }
        return $return;
    }

    public function formatItemByLocation(array $items){
        $tmp = [];

        $checkSet = function(&$arr, $key, $is_arr = true){
            if (!isset($arr[$key])){
                $arr[$key] = $is_arr ? [] : null;
            }
        };

        foreach ($items as $i){
            $descriptors = $i->getDescriptors();

            $r = $descriptors['region'];
            $c = $descriptors['constellation'];
            $ss = $descriptors['solar_system'];
            $station = $descriptors['station'];
            $checkSet($tmp, $r);
            $checkSet($tmp[$r], $c);
            $checkSet($tmp[$r][$c], $ss);
            $checkSet($tmp[$r][$c][$ss], $station);
            $tmp[$r][$c][$ss][$station][] = $i;
        }

        return $tmp;
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
