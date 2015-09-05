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
        $sql = "SELECT flagText as flag_text FROM {$this->getTableName()} WHERE flagID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $flagId]);

        return $stmt->fetch();

    }

}