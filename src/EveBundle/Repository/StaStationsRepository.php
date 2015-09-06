<?php

namespace EveBundle\Repository;

class StaStationsRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveBundle:StaStations';
    }

    public function getTableName(){
        return 'staStations';
    }

    public function getLocationInfo($locationID){
        $sql = "SELECT
                regionID as region,
                solarSystemID as solar_system,
                constellationID as constellation,
                stationName as station_name
                FROM {$this->getTableName()}
                WHERE stationID = :id ";

        /*
         * Per legacy ID compatibility described
         * http://wiki.eve-id.net/APIv2_Corp_AssetList_XML
         */
        if ($locationID >= 60014861 && $locationID <= 60014928) {
            //@TODO conquerable stations
            return [];
        }

        if ($locationID < 66000000 && $locationID > 61000000){
            //@TODO conquerable object
            return [];
        }

        if ($locationID >= 66000000 && $locationID < 67000000 ){
            $tmp = $locationID - 6000001;
            $locationID = $tmp;
        }

        if ($locationID >= 67000000 && $locationID < 68000000 ){
            $tmp = $locationID - 6000000;
            $locationID = $tmp;
        }


        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $locationID]);

        return $stmt->fetch();

    }

}