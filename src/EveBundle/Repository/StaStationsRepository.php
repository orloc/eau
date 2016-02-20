<?php

namespace EveBundle\Repository;

class StaStationsRepository extends AbstractDbalRepository implements RepositoryInterface
{
    public function getName()
    {
        return 'EveBundle:StaStations';
    }

    public function getTableName()
    {
        return 'staStations';
    }

    public function getStationById($locationID)
    {
        $sql = "SELECT *FROM {$this->getTableName()}
                WHERE stationID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $locationID]);

        return $stmt->fetch();
    }
}
