<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Account;
use AppBundle\Entity\Asset;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EveBundle\Entity\AveragePrice;
use EveBundle\Repository\Registry as EveRegistry;

abstract class AbstractManager {

    protected $doctrine;

    protected $registry;

    public function __construct(Registry $doctrine, EveRegistry $registry){
        $this->doctrine = $doctrine;
        $this->registry = $registry;
    }

    /**
     * @TODO I need to go into my own place
     */
    public function updateResultSet(array $items){

        /*
    $itemTypes = $this->registry->get('EveBundle:ItemType');
    $regions = $this->registry->get('EveBundle:Region');
    $constellations = $this->registry->get('EveBundle:Constellation');
    $solarsystems = $this->registry->get('EveBundle:SolarSystem');
    $locations = $this->registry->get('EveBundle:StaStations');

    $mapDenormalize = $this->registry->get('EveBundle:MapDenormalize');

    foreach ($items as $i){
        $iData = $itemTypes->getItemTypeData($i->getTypeId());

        if ($i->getLocationId() !== null){
            $location = $mapDenormalize->getLocationInfoById($i->getLocationId());
            //cache these
            var_dump($location);die;
            $r = $regions->getRegionById($location['region']);
        } else {
            $parent = $i->getParent();

            while ($parent->getParent() instanceof Asset){
                $parent = $parent->getParent();
            }

        }


        $locationData = $locations->getLocationInfo(
            $i instanceof Asset ? $i->getLocationId() : $i->getPlacedAtId()
        );


        if (count($locationData)){
            $updateData = array_merge(
                $itemTypes->getItemTypeData($i->getTypeId()),
                is_array(($ss = $solarsystems->getSolarSystemById($locationData['solar_system']))) ? $ss : [],
                ['station' => $locationData['station_name']]
            );

            $i->setDescriptors($updateData);
        }

        }

        return $items;
        */
    }

    public function buildTransactionParams(Account $acc, $fromID = null){
        $params =  [
            'accountKey' => $acc->getDivision(),
            'rowCount' => 2000
        ];

        if ($fromID){
            $params = array_merge($params, [ 'fromID' => $fromID]);
        }

        return $params;
    }

}