<?php

namespace EveBundle\Repository;

class ControlTowerResourceRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveBundle:ControlTowerResource';
    }

    public function getFuelConsumption($itemId){
        $sql = "SELECT * FROM {$this->getTableName()}
                WHERE controlTowerTypeID = :id
                AND  minSecurityLevel IS NULL
                AND purpose = '1'
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $itemId]);

        return $stmt->fetch();

    }

    public function getResources(array $ids = []){
        $stmt = $this->conn->executeQuery("SELECT * FROM {$this->getDetailTableName()} WHERE controlTowerTypeID IN (?)",
            array($ids),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();

        return $stmt->fetchAll();

    }

    public function getTableName(){
        return 'invControlTowerResources';
    }

    public function getPurposeTableName(){
        return 'invControlTowerResourcePurposes';
    }

}

