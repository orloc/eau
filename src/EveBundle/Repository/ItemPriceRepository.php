<?php

namespace EveBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ItemPriceRepository extends EntityRepository {

    public function hasItem(\DateTime $date, $region_id, $item_id){
        return $this->createQueryBuilder('ip')
            ->select('ip')
            ->where('ip.date = :date')
            ->andWhere('ip.region_id = :region')
            ->andWhere('ip.type_id = :type_id')
            ->setParameters([
                'type_id' => $item_id,
                'region' => $region_id,
                'date' => $date
            ])->getQuery()->getOneOrNullResult();

    }

}

