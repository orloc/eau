<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Corporation;
use AppBundle\Entity\MarketOrder;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class MarketOrderRepository extends EntityRepository
{

    public function getOrdersByCorporation(Corporation $corp){
        return $this->createQueryBuilder('mo')
            ->where('mo.corporation = :corp')
            ->setParameter('corp', $corp)
            ->getQuery()->getResult();
    }

    public function getOpenBuyOrders(Corporation $corp){
        return $this->createQueryBuilder('mo')
            ->where('mo.corporation = :corp')
            ->andWhere('mo.bid = :bid')
            ->andWhere('mo.state = :state')
            ->setParameter('state', MarketOrder::OPEN)
            ->setParameter('corp', $corp)
            ->setParameter('bid', 1)
            ->getQuery()->getResult();

    }

    public function getOpenSellOrders(Corporation $corp){

        return $this->createQueryBuilder('mo')
            ->where('mo.corporation = :corp')
            ->andWhere('mo.bid = :bid')
            ->andWhere('mo.state = :state')
            ->setParameter('state', MarketOrder::OPEN)
            ->setParameter('corp', $corp)
            ->setParameter('bid', 0)
            ->getQuery()->getResult();
    }

    public function hasOrder(Corporation $corp, $placedById, $placedAtId, $issued, $type_id, $order_id)
    {

        return $this->createQueryBuilder('mo')
            ->where('mo.corporation = :corp')
            ->andWhere('mo.placed_by_id = :placed_by_id')
            ->andWhere('mo.placed_at_id= :placed_at_id')
            ->andWhere('mo.order_id= :order_id')
            ->andWhere('mo.issued = :issued')
            ->andWhere('mo.type_id = :type_id')
            ->setParameters([
                'corp' => $corp,
                'placed_by_id' => $placedById,
                'order_id' => $order_id,
                'placed_at_id' => $placedAtId,
                'issued' => $issued,
                'type_id' => $type_id
            ])
            ->getQuery()->getOneOrNullResult();
    }
}
