<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Account;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;

class JournalTransactionRepository extends EntityRepository {

    public function getTransactionsByAccount(Account $account,Carbon $date){
        $start = $date->copy();
        $start->setTime(0,0,0);

        $end = $start->copy();
        $end->setTime(23,59,59);

        return $this->createQueryBuilder('jt')
            ->select('jt')
            ->where('jt.account = :account')
            ->andWhere('jt.date >= :start')
            ->andWhere('jt.date <= :end')
            ->setParameters([
                'account' => $account,
                'start' => $start,
                'end' => $end
            ])
            ->getQuery()->getResult();
    }

    public function hasTransaction(Account $acc, $refId, $amount){
        return $this->createQueryBuilder('jt')
            ->leftJoin('jt.account', 'acc')
            ->where('acc = :account')
            ->andWhere('jt.ref_id = :id')
            ->andWhere('jt.amount = :amount')
            ->setParameters(['account' => $acc, 'id' => $refId, 'amount' => $amount])
            ->getQuery()->getOneOrNullResult();
    }
}
