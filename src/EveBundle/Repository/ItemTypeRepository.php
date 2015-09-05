<?php

namespace EveBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ItemTypeRepository extends EntityRepository {

    public function getItemTypeData($typeId){
        $result = $this->createQueryBuilder('it')
            ->select('it.name as name, it.description as description')
            ->where('it.type_id = :type')
            ->setParameter('type', $typeId)
            ->getQuery()->getOneOrNullResult();

        return $result;
    }

}

