<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {

    public function getUsers(){
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.deleted_at IS NULL');

    }

    public function findUserBy(array $criteria){

    }

}