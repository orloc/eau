<?php

namespace EveBundle\Repository;

class InvFlagRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveData:InvFlag';
    }

    public function getTableName(){
        return 'invflags';
    }

    public function getFlagName($flagId){
        $sql = "SELECT flagName, flagText FROM {$this->getTableName()} WHERE flagID = :id";

        $stmt = $this->conn->prepare($sql);

        $result = $stmt->execute(['id' => $flagId]);

        var_dump($result);die;

    }

}