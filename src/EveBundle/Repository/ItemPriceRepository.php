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

    public function getRegionIds(){
        $res = $this->createQueryBuilder('ip')
            ->select('distinct ip.region_id as region_id')
            ->getQuery()->getResult();

        return array_values(array_map(function($d){
            return $d['region_id'];
        }, $res));
    }

    public function getItem($region_id, $item_id){
        return $this->createQueryBuilder('ip')
            ->select('ip')
            ->andWhere('ip.region_id = :region')
            ->andWhere('ip.type_id = :type_id')
            ->orderBy('ip.date', 'DESC')
            ->setMaxResults(1)
            ->setParameters([
                'type_id' => $item_id,
                'region' => $region_id
            ])->getQuery()->getOneOrNullResult();
    }

    public function getItems($region_id, array $items){
        return $this->createQueryBuilder('ip')
            ->select('ip')
            ->andWhere('ip.region_id = :region')
            ->andWhere('ip.type_id IN ( :type_ids )')
            ->orderBy('ip.date', 'DESC')
            ->addGroupBy('ip.type_id')
            ->setParameters([
                'type_ids' => $items,
                'region' => $region_id
            ])->getQuery()->getResult();

    }

}

