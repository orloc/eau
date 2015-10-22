<?php

namespace AppBundle\Service;

use AppBundle\Entity\Asset;
use EveBundle\Repository\Registry as EveRegistry;

class AssetDetailUpdateManager {

    protected $registry;

    protected $cache;

    public function __construct(EveRegistry $registry){
        $this->registry = $registry;
        $this->cache = [];

    }

    public function updateDetails(array $items){
        $itemTypes = $this->registry->get('EveBundle:ItemType');
        $regions = $this->registry->get('EveBundle:Region');
        $constellations = $this->registry->get('EveBundle:Constellation');
        $solarsystems = $this->registry->get('EveBundle:SolarSystem');

        foreach ($items as $i){
            $iData = $itemTypes->getItemTypeData($i->getTypeId());

            if ($i->getLocationId() !== null){
                if (!$this->hasItem('location', $i->getLocationId())){
                    $location = $this->determineLocationDetails($i);

                    if ($location !== null){
                        $location['regionID'] = $regions->getRegionById($location['regionID']);
                        $location['constellationID'] = $constellations->getConstellationById($location['constellationID']);

                        if ($location['solarSystemID'] !== null){
                            $location['solarSystemID'] = $solarsystems->getSolarSystemById($location['solarSystemID']);

                        }

                        $this->cacheItem('location', $i->getLocationId(), $location);
                    }
                } else {
                    $location = $this->cache['location'][$i->getLocationId()];
                }

            } else {
                $parent = $i->getParent();

                while ($parent->getParent() instanceof Asset){
                    $parent = $parent->getParent();
                }

                if ($this->hasItem('location', $parent->getLocationId())){
                    $location = $this->cache['location'][$parent->getLocationId()];
                }
            }


        }

        die;
    }

    public function determineLocationDetails($i){
        $id = (int)$i->getLocationId();
        $mapDenormalize = $this->registry->get('EveBundle:MapDenormalize');
        $stationRepo = $this->registry->get('EveBundle:StaStations');

        if ($id < 60000000) {
            return $mapDenormalize->getLocationInfoById($id);
        }

        if ($id >= 60014861 && $id  <=60014928){
            // conqStation lookup
        }

        if ($id >= 66000000 && $id <= 66014933){
            return $stationRepo->getStationById($id-6000001);
        }

        if ($id >= 66014934 && $id <= 67999999){
            // conqStation lookup - 6000000
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