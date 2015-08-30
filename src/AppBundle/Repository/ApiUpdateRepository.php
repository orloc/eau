<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Corporation;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class ApiUpdateRepository extends EntityRepository {

    public function getShortTimerExpired(Corporation $entity){

        $now = Carbon::create()->subMinutes(30);

        return $this->createQueryBuilder('au')
            ->select('au')
            ->leftJoin('au.corporation', 'c')
            ->where('c = :corp')
            ->andWhere('au.created_at < :now')
            ->setParameters([
                'corp' => $entity , 'now' => $now
            ])->getQuery()->getResult();

    }

    public function getLongTimerExpired(Corporation $entity){

        $now = Carbon::create()->subDay();

        return $this->createQueryBuilder('au')
            ->select('au')
            ->leftJoin('au.corporation', 'c')
            ->where('c = :corp')
            ->andWhere('au.created_at < :now')
            ->setParameters([
                'corp' => $entity , 'now' => $now
            ])->getQuery()->getResult();

    }
}
