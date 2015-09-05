<?php

namespace EveBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RegionRepository extends EntityRepository {

    public function getRegionById($regionId){
        $result = $this->createQueryBuilder('r')
            ->select('r.name as name')
            ->where('r.region_id = :id')
            ->setParameter('id', $regionId)
            ->getQuery()->getOneOrNullResult();

        return $result;
    }

}

