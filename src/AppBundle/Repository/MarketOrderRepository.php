<?php

namespace AppBundle\Repository;

use AppBundle\Entity\MarketOrder;
use AppBundle\Entity\MarketOrderGroup;
use Doctrine\ORM\EntityRepository;

class MarketOrderRepository extends EntityRepository
{

    public function getOrdersByMarketGroup(MarketOrderGroup $group){
        return $this->createQueryBuilder('mo')
            ->where('mo.market_order_group = :mgroup')
            ->setParameter('mgroup', $group)
            ->getQuery()->getResult();
    }

    public function getOpenBuyOrders(MarketOrderGroup $group){
        return $this->createQueryBuilder('mo')
            ->andWhere('mo.bid = :bid')
            ->andWhere('mo.state = :state')
            ->andWhere('mo.market_order_group = :mgroup ')
            ->setParameter('state', MarketOrder::OPEN)
            ->setParameter('mgroup', $group)
            ->setParameter('bid', 1)
            ->getQuery()->getResult();

    }

    public function getBuyOrders(MarketOrderGroup $group){
        return $this->createQueryBuilder('mo')
            ->andWhere('mo.bid = :bid')
            ->andWhere('mo.market_order_group = :mgroup ')
            ->setParameter('mgroup', $group)
            ->setParameter('bid', 1)
            ->getQuery()->getResult();

    }

    public function getSellOrders(MarketOrderGroup $group){
        return $this->createQueryBuilder('mo')
            ->andWhere('mo.bid = :bid')
            ->andWhere('mo.market_order_group = :mgroup ')
            ->setParameter('mgroup', $group)
            ->setParameter('bid', 0)
            ->getQuery()->getResult();

    }

    public function getOpenSellOrders(MarketOrderGroup $group){

        return $this->createQueryBuilder('mo')
            ->andWhere('mo.bid = :bid')
            ->andWhere('mo.state = :state')
            ->andWhere('mo.market_order_group = :mgroup ')
            ->setParameter('state', MarketOrder::OPEN)
            ->setParameter('mgroup', $group)
            ->setParameter('bid', 0)
            ->getQuery()->getResult();
    }
}
