<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;

class JournalTransactionRepository extends EntityRepository {

    public function getLatestTransactionForAccount(Account $account){
        return $this->createQueryBuilder('jt')
            ->select('jt')
            ->leftJoin('jt.account', 'acc')
            ->andWhere('acc = :acc')
            ->orderBy('jt.date', 'DESC')
            ->setMaxResults(1)
            ->setParameter('acc', $account)
            ->getQuery()->getOneOrNullResult();
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
