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
}
