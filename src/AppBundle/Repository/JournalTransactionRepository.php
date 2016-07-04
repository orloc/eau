<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Account;
use AppBundle\Entity\Corporation;
use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class JournalTransactionRepository extends EntityRepository
{
    protected function getTransactionByAccountQuery(Account $account, Carbon $start, Carbon $end)
    {
        return $this->createQueryBuilder('jt')
            ->select('jt')
            ->where('jt.account = :account')
            ->andWhere('jt.date >= :start')
            ->andWhere('jt.date <= :end')
            ->setParameters([
                'account' => $account,
                'start' => $start,
                'end' => $end,
            ])
            ->getQuery()->getResult();
    }

    public function getTransactionsByAccount(Account $account, Carbon $date)
    {
        $start = $date->copy();
        $start->setTime(0, 0, 0);

        $end = $start->copy();
        $end->setTime(23, 59, 59);

        return $this->getTransactionByAccountQuery($account, $start, $end);
    }

    public function getTransactionsByAccountInRange(Account $account, array $range)
    {
        $start = $range['start']->copy();
        $start->setTime(0, 0, 0);

        $end = $range['end']->copy();
        $end->setTime(23, 59, 59);

        return $this->getTransactionByAccountQuery($account, $start, $end);
    }

    public function getTransactionsByTypes(Corporation $corp, array $types, Carbon $date)
    {
        $start = $date->copy();
        $start->subMonth()->setTime(0, 0, 0);

        $end = $date->copy();
        $end->setTime(23, 59, 59);

        $sql = 'SELECT jt.ref_type_id, group_concat(DISTINCT jt.id) as ids
             FROM journal_transactions as jt
             LEFT JOIN accounts as acc on jt.account_id=acc.id
             WHERE acc.corporation_id = :corp_id
             AND jt.ref_type_id IN ( :ref_types )
             AND jt.date >= :start_date
             AND jt.date <= :end_date
             GROUP BY jt.ref_type_id';

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('AppBundle\Entity\JournalTransaction', 'jt');
        $rsm->addFieldResult('jt', 'ref_type_id', 'ref_type_id');
        $rsm->addFieldResult('jt', 'ids', 'id');
        $q = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        $q->setParameter('corp_id', $corp->getId());
        $q->setParameter('ref_types', $types, Connection::PARAM_INT_ARRAY);
        $q->setParameter('start_date', $start);
        $q->setParameter('end_date', $end);

        $results = $q->getResult();

        $real_res = [];
        foreach ($results as $res) {
            $ids = explode(',', $res->getId());

            $r = $this->createQueryBuilder('jt')
                ->select('sum(jt.amount) as total_amount')
                ->where('jt.id in (:j_ids)')
                ->setParameter('j_ids', $ids)
                ->getQuery()->getResult();

            $real_res[] = [
                'type' => $res->getRefType(),
                'trans' => $r,
                'orig_ids' => $ids,
            ];
        }

        return $real_res;
    }

    public function getTransactionsByMember(Corporation $corp, array $member_ids, Carbon $date)
    {
        $start = $date->copy();
        $start->subMonth()->setTime(0, 0, 0);

        $end = $date->copy();
        $end->setTime(23, 59, 59);

        $sql = 'SELECT jt.owner_id2, group_concat(DISTINCT jt.id) as ids
            FROM journal_transactions as jt
            LEFT JOIN accounts as acc on jt.account_id=acc.id
            WHERE  acc.corporation_id = :corp_id
            AND jt.owner_id2 in ( :owner_ids )
            AND jt.date >= :start_date
            AND jt.date <= :end_date
            GROUP BY jt.owner_id2';

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('AppBundle\Entity\JournalTransaction', 'jt');
        $rsm->addFieldResult('jt', 'owner_id2', 'owner_id2');
        $rsm->addFieldResult('jt', 'ids', 'id');
        $q = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        $q->setParameter('corp_id', $corp->getId());
        $q->setParameter('owner_ids', $member_ids, Connection::PARAM_INT_ARRAY);
        $q->setParameter('start_date', $start);
        $q->setParameter('end_date', $end);

        $results = $q->getResult();

        $real_res = [];
        foreach ($results as $res) {
            $ids = explode(',', $res->getId());

            $rt = $this->createQueryBuilder('jt')
                ->select('sum(jt.amount) as total_amount')
                ->where('jt.id in (:j_ids)')
                ->setParameter('j_ids', $ids)
                ->getQuery()->getResult();
            $r = $this->createQueryBuilder('jt')
                ->select('jt')
                ->where('jt.id in (:j_ids)')
                ->setParameter('j_ids', $ids)
                ->getQuery()->getResult();

            $real_res[] = [
                'user' => $res->getOwnerId2(),
                'total' => $rt,
                'orig_ids' => $r,
            ];
        }

        return  $real_res;
    }

    public function hasTransaction(Account $acc, $refId, $amount)
    {
        return $this->createQueryBuilder('jt')
            ->leftJoin('jt.account', 'acc')
            ->where('acc = :account')
            ->andWhere('jt.ref_id = :id')
            ->andWhere('jt.amount = :amount')
            ->setParameters(['account' => $acc, 'id' => $refId, 'amount' => $amount])
            ->getQuery()->getOneOrNullResult();
    }
}
