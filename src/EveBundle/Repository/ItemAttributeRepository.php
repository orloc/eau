<?php

namespace EveBundle\Repository;

class ItemAttributeRepository extends AbstractDbalRepository implements RepositoryInterface
{
    public function getName()
    {
        return 'EveBundle:ItemAttribute';
    }

    public function getItemAttributes($itemId)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE typeID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $itemId]);

        return $stmt->fetchAll();
    }

    public function getAttributes(array $ids = [])
    {
        $stmt = $this->conn->executeQuery("SELECT * FROM {$this->getDetailTableName()} WHERE attributeID IN (?)",
            array($ids),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getTableName()
    {
        return 'dgmTypeAttributes';
    }

    public function getDetailTableName()
    {
        return 'dgmAttributeTypes';
    }
}
