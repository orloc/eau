<?php

namespace AppBundle\Repository;


use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\AssetGrouping;
use Doctrine\ORM\EntityRepository;

class AssetRepository extends EntityRepository {

    public function getTopLevelAssetsByGroup(AssetGroup $group){
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.asset_group = :group')
            ->andWhere('a.parent IS NULL')
            ->andWhere('a.flag_id != :flag')
            ->andWhere('a.flag_id != :flag2')
            ->setParameter('group', $group)
            ->setParameter('flag', 62) // does not equal deliveries
            ->setParameter('flag2', 0) // anchored structures
            ->getQuery();
    }

    public function getDeliveriesByGroup(AssetGroup $group){
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.asset_group = :group')
            ->andWhere('a.flag_id = :flag')
            ->setParameter('group', $group)
            ->setParameter('flag', 62) // does not equal deliveries
            ->getQuery();
    }

}
