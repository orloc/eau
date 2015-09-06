<?php

namespace EveBundle\Repository;

class ConstellationRepository extends AbstractDbalRepository implements RepositoryInterface {

    public function getName(){
        return 'EveBundle:Constellation';
    }

    public function getTableName(){
        return 'mapConstellations';
    }

    public function getConstellationById($constellationId){

        $sql = "SELECT constellationName as constellation FROM {$this->getTableName()} WHERE constellationID = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $constellationId]);

        return $stmt->fetch();

    }

}

