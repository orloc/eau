<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;

class MarketTransactionRepository extends EntityRepository {

    public function hasTransaction(Account $acc, $refId){
        return $this->createQueryBuilder('jt')
            ->leftJoin('jt.account', 'acc')
            ->where('acc = :account')
            ->andWhere('jt.ref_id = :id')
            ->setParameters(['account' => $acc, 'id' => $refId])
            ->getQuery()->getOneOrNullResult();
    }
}
