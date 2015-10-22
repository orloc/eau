<?php

namespace EveBundle\Repository;

class MapDenormalizeRepository extends AbstractDbalRepository implements RepositoryInterface
{

    public function getName()
    {
        return 'EveBundle:MapDenormalize';
    }

    public function getTableName()
    {
        return 'mapDenormalize';
    }

    public function getLocationInfoById($typeId){

        $sql = "SELECT
                *
                FROM {$this->getTableName()}
                WHERE itemID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $typeId]);

        return $stmt->fetch();
    }
}
