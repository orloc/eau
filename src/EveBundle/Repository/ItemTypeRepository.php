<?php

namespace EveBundle\Repository;

class ItemTypeRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveBundle:ItemType';
    }

    public function getTableName(){
        return 'invTypes';
    }

    public function getItemTypeData($typeId){

        $sql = "SELECT typeName as name, description as description FROM {$this->getTableName()} WHERE typeID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $typeId]);

        return $stmt->fetch();

    }

}

