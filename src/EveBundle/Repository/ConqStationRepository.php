<?php

namespace EveBundle\Repository;

class ConqStationRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveBundle:StaStations';
    }

    public function getTableName(){
        return 'staStations';
    }

    public function getStationById($locationID){
    }

}
