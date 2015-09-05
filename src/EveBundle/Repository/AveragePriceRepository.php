<?php

namespace EveBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AveragePriceRepository extends EntityRepository {

    public function getAveragePriceByType($typeId){
        $result = $this->createQueryBuilder('ap')
            ->select('ap')
            ->where('r.type_id = :id')
            ->setParameter('id', $typeId)
            ->getQuery()->getOneOrNullResult();

        return $result;
    }

}

