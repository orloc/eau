<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Corporation;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class ApiUpdateRepository extends EntityRepository {

    public function getShortTimerExpired(Corporation $entity, $call){
        $now = Carbon::create()->subMinutes(35);

        return (bool)$this->getTimeExpiredBuilder($entity, $now, $call);
    }

    public function getLongTimerExpired(Corporation $entity, $call){
        $now = Carbon::create()->subDay();

        return (bool)$this->getTimeExpiredBuilder($entity, $now, $call);
    }

    public function getLastUpdateByCorpType(Corporation $entity, $type){
        return $this->createQueryBuilder('au')
            ->select('au')
            ->where('au.corporation = :corp')
            ->andWhere('au.type = :type')
            ->orderBy('au.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameters(['corp' => $entity, 'type' => $type])
            ->getQuery()->getOneOrNullResult();
    }

    private function getTimeExpiredBuilder(Corporation $entity, Carbon $now, $call){
        $is_cached = $this->createQueryBuilder('au')
            ->select('count(au) as is_cached')
            ->leftJoin('au.corporation', 'c')
            ->where('c = :corp')
            ->andWhere('au.created_at > :now')
            ->andWhere('au.api_call = :call')
            ->addOrderBy('au.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameters([
                'corp' => $entity , 'now' => $now, 'call' => $call
            ])->getQuery()->getOneOrNullResult();


        if (isset($is_cached['is_cached'])){
            return $is_cached['is_cached'];
        }

        return true;
    }
}
