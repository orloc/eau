<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getUsers()
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.deleted_at IS NULL')
            ->getQuery()->getResult();
    }

    public function findAllByCharacterNames(array $list)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.characters', 'c')
            ->where('c.name IN ( :name_list )')
            ->setParameter('name_list', $list)
            ->getQuery()->getResult();
    }

    public function findAllByCorporationIds(array $list)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.characters', 'c')
            ->where('c.eve_corporation_id IN ( :id_list )')
            ->setParameter('id_list', $list)
            ->getQuery()->getResult();
    }
}
