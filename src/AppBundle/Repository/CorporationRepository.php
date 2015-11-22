<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CorporationRepository extends EntityRepository {

    public function findAllUpdatedCorporations(){
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.corporation_details', 'cd')
            ->where('cd IS NOT NULL')
            ->getQuery()->getResult();

    }

    public function findToBeUpdatedCorporations(){
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.corporation_details', 'cd')
            ->where('cd IS NULL')
            ->getQuery()->getResult();

    }

    public function findCorporationsByAlliance($allianceName){
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.corporation_details', 'cd')
            ->where('cd.alliance_name = :allianceName')
            ->setParameter('allianceName' , $allianceName)
            ->getQuery()->getResult();
    }

    public function findByCorpName($corpName){
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.corporation_details', 'cd')
            ->where('cd.name = :corpName')
            ->setParameter('corpName' , $corpName)
            ->getQuery()->getOneOrNullResult();
    }


}
