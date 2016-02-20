<?php

namespace EveBundle\Repository;

class ItemTypeRepository extends AbstractDbalRepository implements RepositoryInterface
{
    public function getName()
    {
        return 'EveBundle:ItemType';
    }

    public function getTableName()
    {
        return 'invTypes';
    }

    public function getItemTypeData($typeId)
    {
        $sql = "SELECT typeName as name, description as description, volume as volume, marketGroupID as market_group, groupID as group_id FROM {$this->getTableName()} WHERE typeID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $typeId]);

        return $stmt->fetch();
    }

    public function findAllInGroups(array $groupIds)
    {
        $stmt = $this->conn->executeQuery("SELECT * FROM {$this->getTableName()} WHERE marketGroupID IN (?)",
            array($groupIds),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findAllByTypes(array $types)
    {
        $stmt = $this->conn->executeQuery("SELECT * FROM {$this->getTableName()} WHERE typeID IN (?)",
            array($types),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findAllMarketItems()
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE marketGroupID IS NOT NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTypesByName(array $types = [])
    {
        $stmt = $this->conn->executeQuery("SELECT * FROM {$this->getTableName()} WHERE typeName IN (?)",
            array($types),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTypesByGroupId($groupId)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE marketGroupID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $groupId]);

        return $stmt->fetchAll();
    }
}
