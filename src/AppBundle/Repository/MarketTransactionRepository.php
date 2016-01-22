<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Account;
use AppBundle\Entity\Corporation;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class MarketTransactionRepository extends EntityRepository {

     protected function getTransactionByAccountQuery(Account $account, Carbon $start, Carbon $end){
        return $this->createQueryBuilder('mt')
            ->select('mt')
            ->where('mt.account = :account')
            ->andWhere('mt.date >= :start')
            ->andWhere('mt.date <= :end')
            ->setParameters([
                'account' => $account,
                'start' => $start,
                'end' => $end
            ]);
    }

    public function getTransactionsByAccount(Account $account,Carbon $date){
        $start = $date->copy();
        $start->setTime(0,0,0);

        $end = $start->copy();
        $end->setTime(23,59,59);

        return $this->getTransactionByAccountQuery($account, $start, $end)->getQuery()->getResult();

    }

    public function getTransactionsByAccountInRange(Account $account, array $range, $type){

        $start = $range['start']->copy();
        $start->setTime(0,0,0);

        $end = $range['end']->copy();
        $end->setTime(23,59,59);

        $q = $this->getTransactionByAccountQuery($account, $start, $end)
                ->andWhere('mt.transaction_type = :trans_type')
                ->setParameter('trans_type', $type);

        return $q->getQuery()->getResult();
    }

    public function hasTransaction(Account $acc, $transactionId, $jTransID){
        return $this->createQueryBuilder('mt')
            ->leftJoin('mt.account', 'acc')
            ->where('acc = :account')
            ->andWhere('mt.transaction_id = :id')
            ->andWhere('mt.journal_transaction_id = :mtid')
            ->setParameters(['account' => $acc, 'id' => $transactionId, 'mtid' => $jTransID])
            ->getQuery()->getOneOrNullResult();
    }

    public function findLatestTransactionByItemType(Corporation $corp, $type, $itemID){
        return $this->createQueryBuilder('mt')
            ->select('mt')
            ->leftJoin('mt.account', 'acc')
            ->andWhere('acc.corporation = :corporation')
            ->andWhere('mt.item_id = :item_id')
            ->andWhere('mt.transaction_type = :type')
            ->orderBy('mt.date', 'DESC')
            ->setParameters([
                'corporation' => $corp,
                'item_id' => $itemID,
                'type' => $type
            ])
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getTotalBuyForDate(Account $acc, Carbon $date){
        return $this->getTotalByTypeDate('buy', $acc, $date)->getQuery()->getResult();
    }

    public function getTotalSellForDate(Account $acc, Carbon $date){
        return $this->getTotalByTypeDate('sell', $acc, $date)->getQuery()->getResult();
    }

    protected function getTotalByTypeDate($type, Account $acc, Carbon $date){
        $start = $date->copy();
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
