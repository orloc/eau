<?php

namespace EveBundle\Repository;

class RegionRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveBundle:Region';
    }

    public function getTableName(){
        return 'mapRegions';
    }

    public function getRegionById($regionId){

        $sql = "SELECT * FROM {$this->getTableName()} WHERE regionID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $regionId]);

        return $stmt->fetch();

    }

    public function getRegionsInList(array $regions){
        $stmt = $this->conn->executeQuery("SELECT * FROM {$this->getTableName()} WHERE regionID IN (?)",
            array($regions),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAll(){
        $sql = "SELECT * FROM {$this->getTableName()}";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

}

