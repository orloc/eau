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
    public function updatePrices(array $items){
        $prices = $this->doctrine->getManager('eve_data')
            ->getRepository('EveBundle:AveragePrice');

        $arrType = [];
        foreach ($items as $i){
            // @TODO I think this is of type Asset
            if (is_object($i)){
                $descriptors = $i->getDescriptors();

                $price = $prices->getAveragePriceByType($i->getTypeId());
                $descriptors['price'] = $price instanceof AveragePrice
                    ? floatval($price->getAveragePrice())
                    : 0;

                $descriptors['total_price'] = floatval($descriptors['price'] * $i->getQuantity());
                $i->setDescriptors($descriptors);

            } elseif (is_array($i)) {
                $price = $prices->getAveragePriceByType($i['typeID']);
                $i['price'] = $price instanceof AveragePrice
                    ? floatval($price->getAveragePrice())
                    : 0;

                $arrType[] = $i;

            }
        }

        if (count($arrType)){
            return $arrType;
        }
    }

    /**
     * @TODO I need to go into my own place
     */
    public function updateResultSet(array $items){
        $itemTypes = $this->registry->get('EveBundle:ItemType');
        $regions = $this->registry->get('EveBundle:Region');
        $constellations = $this->registry->get('EveBundle:Constellation');
        $solarsystems = $this->registry->get('EveBundle:SolarSystem');
        $locations = $this->registry->get('EveBundle:StaStations');

        foreach ($items as $i){
            $locationData = $locations->getLocationInfo(
                $i instanceof Asset ? $i->getLocationId() : $i->getPlacedAtId()
            );

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