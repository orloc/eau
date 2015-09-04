<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Corporation;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class ApiUpdateRepository extends EntityRepository {

    public function getShortTimerExpired(Corporation $entity){
        $now = Carbon::create()->subMinutes(35);

        return (bool)$this->getTimeExpiredBuilder($entity, $now);
    }

    public function getLongTimerExpired(Corporation $entity){
        $now = Carbon::create()->subDay();

        return (bool)$this->getTimeExpiredBuilder($entity, $now);
    }

    private function getTimeExpiredBuilder(Corporation $entity, Carbon $now){
        $is_cached = $this->createQueryBuilder('au')
            ->select('count(au) as is_cached')
            ->leftJoin('au.corporation', 'c')
            ->where('c = :corp')
            ->andWhere('au.created_at > :now')
            ->addOrderBy('au.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameters([
                'corp' => $entity , 'now' => $now
            ])->getQuery()->getOneOrNullResult();


        if (isset($is_cached['is_cached'])){
            return $is_cached['is_cached'];
        }

        return true;
    }
}
