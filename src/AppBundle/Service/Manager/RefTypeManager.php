<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\ApiCredentials;
use Doctrine\Bundle\DoctrineBundle\Registry;

class RefTypeManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    public function updateRefTypes(){
        $nullKey = new ApiCredentials();

        $client = $this->getClient($nullKey);

        $response = $client->RefTypes();

        var_dump($response);die;
    }


    public function mapList($items, array $options){

    }

    public function mapItem($item){

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
