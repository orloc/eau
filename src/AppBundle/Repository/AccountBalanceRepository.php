<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;

class AccountBalanceRepository extends EntityRepository {

    public function getLatestBalance(Account $acc){
        return $this->createQueryBuilder('ab')
            ->leftJoin('ab.account', 'acc')
            ->where('acc = :account')
            ->addOrderBy('ab.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameters(['account' => $acc])
            ->getQuery()->getOneOrNullResult();
    }
}
