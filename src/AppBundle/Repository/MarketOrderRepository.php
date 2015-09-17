<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Corporation;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class MarketOrderRepository extends EntityRepository
{

    public function hasOrder(Corporation $corp, $placedById, $placedAtId, $issued, $type_id)
    {
        return $this->createQueryBuilder('mo')
            ->where('mo.corporation = :corp')
            ->andWhere('mo.placed_by_id = :placed_by_id')
            ->andWhere('mo.placed_at_id= :placed_at_id')
            ->andWhere('mo.issued = :issued')
            ->andWhere('mo.type_id = :type_id')
            ->setParameters([
                'corp' => $corp,
                'placed_by_id' => $placedById,
                'placed_at_id' => $placedAtId,
                'issued' => $issued,
                'type_id' => $type_id
            ])
            ->getQuery()->getOneOrNullResult();
    }
}
