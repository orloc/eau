<?php

namespace EveBundle\Repository;

class SolarSystemRepository extends AbstractDbalRepository implements RepositoryInterface
{
    public function getName()
    {
        return 'EveBundle:SolarSystem';
    }

    public function getTableName()
    {
        return 'mapSolarSystems';
    }

    public function getSolarSystemById($ssID)
    {
        $sql = "SELECT solarSystemName as name FROM {$this->getTableName()} WHERE solarSystemID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $ssID]);

        return $stmt->fetch();
    }
}
