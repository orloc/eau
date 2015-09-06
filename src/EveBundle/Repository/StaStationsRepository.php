<?php

namespace EveBundle\Repository;

class StaStationsRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveData:StaStations';
    }

    public function getTableName(){
        return 'stastations';
    }

    public function getLocationInfo($locationID){
        $sql = "SELECT  regionID as region, solarSystemID as solar_system, security as security, stationName as station_name FROM {$this->getTableName()} WHERE stationID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $locationID]);

        return $stmt->fetch();

    }

}