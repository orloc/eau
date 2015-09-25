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

        $sql = "SELECT typeName as name, description as description, volume as volume, marketGroupID as market_group, groupID as group_id FROM {$this->getTableName()} WHERE typeID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $typeId]);

        return $stmt->fetch();

    }

    public function findTypesByGroupId($groupId){
        $sql = "SELECT * FROM {$this->getTableName()} WHERE marketGroupID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $groupId]);

        return $stmt->fetchAll();

    }

}

