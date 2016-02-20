<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CorporationMemberRepository extends EntityRepository
{
    public function getMembers(array $member_ids)
    {
        return $this->createQueryBuilder('cm')
            ->select('cm')
            ->where('cm.id IN ( :member_ids ) ')
            ->setParameter('member_ids', $member_ids)
            ->getQuery()->getResult();
    }
}
