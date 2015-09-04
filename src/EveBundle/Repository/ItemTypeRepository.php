<?php

namespace EveBundle\Repository;


use AppBundle\Entity\Asset;
use Doctrine\ORM\EntityRepository;

class ItemTypeRepository extends EntityRepository {

    public function updateItemTypeData(Asset $asset){
        $result = $this->createQueryBuilder('it')
            ->select('it.name as name, it.description as description')
            ->where('it.type_id = :type')
            ->setParameter('type', $asset->getTypeId())
            ->getQuery()->getOneOrNullResult();

        if ($result){
            $asset->setDescription($result['description'])
                ->setName($result['name']);
        }
    }

}

