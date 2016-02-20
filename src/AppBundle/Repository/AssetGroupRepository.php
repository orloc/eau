<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Corporation;
use Doctrine\ORM\EntityRepository;

class AssetGroupRepository extends EntityRepository
{
    public function getLatestAssetGroup(Corporation $entity)
    {
        return $this->createQueryBuilder('ag')
            ->select('ag')
            ->where('ag.corporation = :corporation')
            ->orderBy('ag.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameter('corporation', $entity)
            ->getQuery()->getOneOrNullResult();
    }

    public function getLatestNeedsUpdateAssetGroupByIds(array $ids)
    {
        $res = $this->createQueryBuilder('ag')
            ->select('max(ag.created_at) created_at')
            ->where('ag.corporation IN ( :corporation_ids )')
            ->andWhere('ag.has_been_updated = 0')
            ->groupBy('ag.corporation')
            ->setParameter('corporation_ids', $ids)
            ->getQuery()->getResult();

        $dates = array_map(function ($r) {
            return $r['created_at'];
        }, $res);

        return $this->createQueryBuilder('ag')
            ->where('ag.created_at in ( :dates ) ')
            ->setParameter('dates', $dates)
            ->getQuery()->getResult();
    }
}
