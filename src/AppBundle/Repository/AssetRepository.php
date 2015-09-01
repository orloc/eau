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
            ->setParameter('group', $group)
            ->getQuery();
    }

}
