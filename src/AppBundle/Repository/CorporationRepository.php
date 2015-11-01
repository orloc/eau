<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CorporationRepository extends EntityRepository {

    public function findAllUpdatedCorporations(){
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.corporation_details', 'cd')
            ->where('cd IS NOT NULL')
            ->getQuery()->getResult();

    }

    public function findToBeUpdatedCorporations(){
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.corporation_details', 'cd')
            ->where('cd IS NULL')
            ->getQuery()->getResult();

    }


}
