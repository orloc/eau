<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Account;
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
}
