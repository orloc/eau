<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Account;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class MarketTransactionRepository extends EntityRepository {

    public function hasTransaction(Account $acc, $transactionId, $jTransID){
        return $this->createQueryBuilder('jt')
            ->leftJoin('jt.account', 'acc')
            ->where('acc = :account')
            ->andWhere('jt.transaction_id = :id')
            ->andWhere('jt.journal_transaction_id = :jtid')
            ->setParameters(['account' => $acc, 'id' => $transactionId, 'jtid' => $jTransID])
            ->getQuery()->getOneOrNullResult();
    }

    public function getTotalBuyForDate(Account $acc, Carbon $date){
        return $this->getTotalByTypeDate('buy', $acc, $date)->getQuery()->getResult();
    }

    public function getTotalSellForDate(Account $acc, Carbon $date){
        return $this->getTotalByTypeDate('sell', $acc, $date)->getQuery()->getResult();
    }

    protected function getTotalByTypeDate($type, Account $acc, CArbon $date){
        $start = clone($date);
        $start->setTime(0,0,0);

        $end = $start->copy();
        $end->setTime(23,59,59);

        return $this->createQueryBuilder('mt')
            ->leftJoin('mt.account', 'acc')
            ->where('acc = :acc')
            ->andWhere('mt.date >= :start')
            ->andWhere('mt.date <= :end')
            ->andWhere('mt.transaction_type = :type')
            ->setParameters([
                'acc' => $acc,
                'start' => $start,
                'end' => $end,
                'type' => $type
            ]);
    }
}
