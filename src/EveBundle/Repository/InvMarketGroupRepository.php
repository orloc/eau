<?php

namespace EveBundle\Repository;

class InvMarketGroupRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveBundle:MarketGroup';
    }

    public function getTableName(){
        return 'invMarketGroups';
    }

    public function getOreGroups(){

        $sql = "SELECT * FROM {$this->getTableName()} WHERE parentGroupID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => 54]);

        return $stmt->fetchAll();

    }

    public function getTopLevelGroups(){
        $sql = "SELECT * FROM {$this->getTableName()} WHERE parentGroupID IS NULL ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getInList(array $list){
        $stmt = $this->conn->executeQuery("SELECT * FROM {$this->getTableName()} WHERE marketGroupID IN (?)",
            array($list),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllGroups(){
        $sql = "SELECT * FROM {$this->getTableName()}";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

}

