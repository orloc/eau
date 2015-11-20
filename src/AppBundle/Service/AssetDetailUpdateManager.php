<?php

namespace AppBundle\Service;

use AppBundle\Entity\Asset;
use AppBundle\Entity\MarketOrder;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EveBundle\Repository\Registry as EveRegistry;

class AssetDetailUpdateManager {

    protected $registry;

    protected $cache;

    protected $doctrine;

    public function __construct(EveRegistry $registry, Registry $doctrine){
        $this->registry = $registry;
        $this->doctrine = $doctrine;
        $this->cache = [];
    }

    public function updateDetails(array $items){
        $itemTypes = $this->registry->get('EveBundle:ItemType');

        $em = $this->doctrine->getManager();

        foreach ($items as $i){
            $locId = $this->determineLocationId($i);

            $iData = $itemTypes->getItemTypeData($i->getTypeId());

            if ($locId !== null){
                $location = $this->checkAndGetLocation($i, $locId);
            } else {
                $parent = $this->getTopMostParent($i);

                $location = $this->checkAndGetLocation($parent, $this->determineLocationId($parent));
            }

            $descriptors = $this->formatDescriptors(array_merge(
                isset($location) ? $location : []
                , $iData !== false ? $iData : []
            ));

            $i->setDescriptors($descriptors);

            $em->persist($i);
        }

        return $items;
    }

    protected function determineLocationId($i){
        return $i instanceof Asset
            ? $i->getLocationId()
            : ( $i instanceof MarketOrder
                ? $i->getPlacedAtId()
                :null
            );
    }

    protected  function checkAndGetLocation($i, $locId){
        $regions = $this->registry->get('EveBundle:Region');
        $constellations = $this->registry->get('EveBundle:Constellation');
        $solarsystems = $this->registry->get('EveBundle:SolarSystem');

        if (!$this->hasItem('location', $locId)){
            $location = $this->determineLocationDetails($locId);

            if ($location !== null){
                $location['regionID'] = $regions->getRegionById($location['regionID']);
                $location['constellationID'] = $constellations->getConstellationById($location['constellationID']);

                if ($location['solarSystemID'] !== null){
                    $location['solarSystemID'] = $solarsystems->getSolarSystemById($location['solarSystemID']);

                }

                $this->cacheItem('location', $locId, $location);
            }
        } else {
            $location = $this->cache['location'][$locId];
        }

        return $location;

    }

    protected function getTopMostParent(Asset $i){
        $parent = $i->getParent();

        while ($parent->getParent() instanceof Asset){
            $parent = $parent->getParent();
        }

        return $parent;

    }

    protected function formatDescriptors($itemData){

        return [
            'name' => isset($itemData['name']) ? $itemData['name'] : null,
            'description' => isset($itemData['description']) ? $itemData['description'] : null,
            'system' => isset($itemData['solarSystemID'])
                ? $itemData['solarSystemID']['name']
                : (isset($itemData['itemName']) ? $itemData['itemName'] : null),
            'constellation' => isset($itemData['constellationID']) ? $itemData['constellationID']['name'] : null,
            'stationName' => isset($itemData['stationName']) ? $itemData['stationName'] : null,
            'region' => isset($itemData['regionID']) ? $itemData['regionID']['regionName'] : null
        ];

    }

    public function determineLocationDetails($i){
        $id = (int)$i;
        $mapDenormalize = $this->registry->get('EveBundle:MapDenormalize');
        $stationRepo = $this->registry->get('EveBundle:StaStations');
        $conqRepo = $this->doctrine->getRepository('AppBundle:ConquerableStation');

        if ($id < 60000000) {
            return $mapDenormalize->getLocationInfoById($id);
        }

        if ($id >= 60014861 && $id  <= 60014928){

            $station = $conqRepo->findOneBy(['station_id' => $id]);

            $location = $mapDenormalize->getLocationInfoBySolarSystem($station->getSolarSystemId());

            return $location;
            // conqStation lookup
        }

        if ($id >= 66000000 && $id <= 66014933){
            return $stationRepo->getStationById($id-6000001);
        }

        if ($id >= 66014934 && $id <= 67999999){
            // conqStation lookup - 6000000
            $station = $conqRepo->findOneBy(['station_id' => $id - 6000000]);
            $location = $mapDenormalize->getLocationInfoBySolarSystem($station->getSolarSystemId());
            return $location;
        }

        if ($id >= 60000000 && $id <= 61000000){
            return $stationRepo->getStationById($id);
        }


    }

    protected function cacheItem($type, $item, $value){

        if (!isset($this->cache[$type])){
            $this->cache[$type] = [];
        }

        if (!isset($this->cache[$type][$item])){
            $this->cache[$type][$item] = $value;
        }
    }

    protected function hasItem($type, $item){
        if (isset($this->cache[$type])){
            return isset($this->cache[$type][$item]);
        }

        return false;
    }
}