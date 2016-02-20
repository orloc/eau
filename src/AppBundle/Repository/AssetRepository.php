<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use Doctrine\ORM\EntityRepository;

class AssetRepository extends EntityRepository
{
    public function getAllByGroup(AssetGroup $group)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.asset_group = :group')
            ->setParameter('group', $group)
            ->getQuery();
    }

    public function getAllByGroups(array $groups)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.asset_group in (:groups)')
            ->setParameter('groups', $groups)
            ->getQuery()->getResult();
    }

    public function getTopLevelAssetsByGroup(AssetGroup $group)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.asset_group = :group')
            ->andWhere('a.parent IS NULL')
            ->andWhere('a.flag_id != :flag')
            ->andWhere('a.flag_id != :flag2')
            ->setParameter('group', $group)
            ->setParameter('flag', 62) // does not equal deliveries
            ->setParameter('flag2', 0) // anchored structures
            ->getQuery();
    }

    public function getNestedAssets(Asset $asset)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.parent = :id')
            ->andWhere('a.flag_id != :flag')
            ->andWhere('a.flag_id != :flag2')
            ->setParameter('id', $asset->getId())
            ->setParameter('flag', 62) // does not equal deliveries
            ->setParameter('flag2', 0) // anchored structures
            ->getQuery();
    }

    public function getDeliveriesByGroup(AssetGroup $group)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.asset_group = :agroup')
            ->andWhere('a.flag_id = :flag')
            ->setParameter('agroup', $group)
            ->setParameter('flag', 62) // does equal deliveries
            ->getQuery();
    }

    public function getLocationsByAssetGroup(AssetGroup $group)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.asset_group = :a_group')
            ->groupBy('a.locationId')
            ->setParameter('a_group', $group)
            ->getQuery()->getResult();
    }

    public function getTypeIDSByAssetGroup(AssetGroup $group)
    {
        return $this->createQueryBuilder('a')
            ->select('distinct a.typeId')
            ->where('a.asset_group = :a_group')
            ->setParameter('a_group', $group)
            ->getQuery()->getResult();
    }

    public function getAssetsByLocation(AssetGroup $group, $locaitonId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.asset_group = :a_group')
            ->andWhere('a.locationId = :locid')
            ->setParameter('a_group', $group)
            ->setParameter('locid', $locaitonId)
            ->getQuery()->getResult();
    }

    public function getAssetCountByLocation(AssetGroup $group, $locaitonId)
    {
        return $this->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.asset_group = :a_group')
            ->andWhere('a.locationId = :locid')
            ->setParameter('a_group', $group)
            ->setParameter('locid', $locaitonId)
            ->getQuery()->getResult();
    }

    public function getAssetItemSummary(AssetGroup $group)
    {
        return $this->createQueryBuilder('a')
            ->select('a.typeId as type_id, sum(a.quantity) as asset_count, a.descriptors as descriptors')
            ->where('a.asset_group = :a_group')
            ->groupBy('a.typeId')
            ->addOrderBy('asset_count', 'DESC')
            ->setParameter('a_group', $group)
            ->getQuery();
    }
}
