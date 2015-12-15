<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Corporation;
use Doctrine\ORM\EntityRepository;

class AssetGroupRepository extends EntityRepository {

    public function getLatestAssetGroup(Corporation $entity){

        return $this->createQueryBuilder('ag')
            ->select('ag')
            ->where('ag.corporation = :corporation')
            ->orderBy('ag.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameter('corporation', $entity)
            ->getQuery()->getOneOrNullResult();

    }

    public function getLatestNeedsUpdateAssetGroupByIds(array $ids){

        return $this->createQueryBuilder('ag')
            ->select('ag')
            ->where('ag.corporation in (:corporation_ids)')
            ->andWhere('ag.has_been_updated = 0')
            ->orderBy('ag.created_at', 'DESC')
            ->setParameter('corporation_ids', $ids)
            ->getQuery()->getResult();

    }
}
