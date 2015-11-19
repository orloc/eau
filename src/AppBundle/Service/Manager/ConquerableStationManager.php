<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\ApiCredentials;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class ConquerableStationManager implements DataManagerInterface, MappableDataManagerInterface {

    private $pheal;

    private $doctrine;

    public function __construct(PhealFactory $pheal, Registry $doctrine){
        $this->pheal = $pheal;
        $this->doctrine = $doctrine;
    }

    public function updateConquerableStations(){
        $nullKey = new ApiCredentials();

        $client = $this->getClient($nullKey);

        $response = $client->ConquerableStationList();

        var_dump($response);die;
    }


    public function mapList($items, array $options){

    }

    public function mapItem($item){

    }


    public function getClient(ApiCredentials $key, $scope = null){

        // we pass a null scope for key for this request
        // as its not needed and publically available
        $client = $this->pheal->createEveOnline();


        return $client;
    }

    public static function getName(){
        return 'conquerable_station_manager';
    }

}
