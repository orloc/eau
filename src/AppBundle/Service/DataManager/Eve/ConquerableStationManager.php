<?php

namespace AppBundle\Service\DataManager\Eve;


use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\ConquerableStation;
use AppBundle\Service\DataManager\MappableDataManagerInterface;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\AbstractManager;


class ConquerableStationManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    public function updateConquerableStations(){
        $nullKey = new ApiCredentials();

        $client = $this->getClient($nullKey);

        $response = $client->ConquerableStationList()
            ->toArray();

        $existing = $this->doctrine->getRepository('AppBundle:ConquerableStation')
            ->findAll();

        $doctrine = $this->doctrine->getManager();

        foreach ($existing as $exists){
            $doctrine->remove($exists);
        }

        $doctrine->flush();

        $this->mapList($response['result']['outposts'], []);

    }

    public function mapList($items, array $options){
        $em = $this->doctrine->getManager();

        foreach ($items as $i){
            $obj = $this->mapItem($i);
            $em->persist($obj);
        }
    }

    public function mapItem($item){
        $station = new ConquerableStation();

        $station->setCorporationId($item['corporationID'])
            ->setCorporationName($item['corporationName'])
            ->setSolarSystemId($item['solarSystemID'])
            ->setStationId($item['stationID'])
            ->setStationName($item['stationName'])
            ->setStationTypeId($item['stationTypeID']);

        return $station;
    }

    public function getClient(ApiCredentials $key, $scope = 'eve'){
        $client = $this->pheal->createEveOnline();
        $client->scope = $scope;

        return $client;
    }

    public static function getName(){
        return 'conquerable_station_manager';
    }

}
