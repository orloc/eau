<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Corporation;
use Doctrine\ORM\EntityRepository;

class AssetGroupRepository extends EntityRepository
{
    public function getLatest(Corporation $entity)
    {
        return $this->createQueryBuilder('ag')
            ->select('ag')
            ->where('ag.corporation = :corporation')
            ->orderBy('ag.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameter('corporation', $entity)
            ->getQuery()->getOneOrNullResult();
    }

    public function getLatestNeedsUpdate(Corporation $c)
    {
        $res = $this->createQueryBuilder('ag')
            ->select('ag')
            ->where('ag.corporation = :corporation )')
            ->andWhere('ag.has_been_updated = 0')
            ->orderBy('ag.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameter('corporation', $c)
            ->getQuery()->getOneOrNullResult();

        return $res;

    }
}
