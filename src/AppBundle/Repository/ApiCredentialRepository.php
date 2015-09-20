<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Corporation;
use Doctrine\ORM\EntityRepository;

class ApiCredentialRepository extends EntityRepository {

    public function getActiveKey(Corporation $corp){

        return $this->createQueryBuilder('api')
            ->select('api')
            ->where('api.active = :active')
            ->andWhere('api.corporation = :corp')
            ->setParameters(['corp' => $corp, 'active' => true])
            ->getQuery()->getOneOrNullResult();

    }
}
