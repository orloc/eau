<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class UserRepository extends EntityRepository {

    public function getUsers(){
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.deleted_at IS NULL')
            ->getQuery()->getResult();

    }

    public function findAllByCharacterNames(array $list){

        return $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.characters', 'c')
            ->where('c.name IN ( :name_list )')
            ->setParameter('name_list', $list)
            ->getQuery()->getResult();

    }

}