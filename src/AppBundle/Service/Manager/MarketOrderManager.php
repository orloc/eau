<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Corporation;
use AppBundle\Entity\MarketOrder;
use AppBundle\Service\EBSDataMapper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class MarketOrderManager implements DataManagerInterface {

    private $pheal;

    private $mapper;

    private $registry;

    private $doctrine;

    public function __construct(PhealFactory $pheal, EBSDataMapper $dataMapper, EveRegistry $registry, Registry $doctrine){
        $this->pheal = $pheal;
        $this->mapper = $dataMapper;
        $this->registry = $registry;
        $this->doctrine = $doctrine;
    }

    public function getMarketOrders(Corporation $corporation){

        $client = $this->getClient($corporation);

        $orders = $client->MarketOrders();

        $marketOrders = $this->mapList($orders->orders, $corporation);

        return $marketOrders;

    }

    public function updateResultSet(array $items){
        $itemTypes = $this->registry->get('EveBundle:ItemType');
        $regions = $this->registry->get('EveBundle:Region');
        $constellations = $this->registry->get('EveBundle:Constellation');
        $solarsystems = $this->registry->get('EveBundle:SolarSystem');
        $locations = $this->registry->get('EveBundle:StaStations');

        foreach ($items as $i){
            $locationData = $locations->getLocationInfo($i->getPlacedAtId());

            $updateData = array_merge(
                $itemTypes->getItemTypeData($i->getTypeId()),
                is_array(($ss = $solarsystems->getSolarSystemById($locationData['solar_system']))) ? $ss : [],
                is_array(($con = $constellations->getConstellationById($locationData['constellation'])))? $con: [],
                is_array(($reg = $regions->getRegionById($locationData['region']))) ? $reg : [],
                ['station' => $locationData['station_name']]
            );

            $i->setDescriptors($updateData);
        }

        return $items;
    }


    public function mapList(array $orders, Corporation $corp){
        $mappedOrders = [];

        $repo = $this->doctrine->getRepository('AppBundle:MarketOrder');
        foreach ($orders as $o){
            $order = $this->mapItem($o);

            $entity = $repo->hasOrder(
                $corp,
                $order->getPlacedById(),
                $order->getPlacedAtId(),
                $order->getIssued(),
                $order->getTypeId()
            );

            if ($entity === null){
                $corp->addMarketOrder($order);
            }

            $mappedOrders[]=$order;
        }

        return $mappedOrders;
    }

    public function mapItem($order){
        $marketOrder = new MarketOrder();

        $marketOrder->setPlacedById($order->orderID)
            ->setPlacedAtId($order->stationID)
            ->setTotalVolume($order->volEntered)
            ->setVolumeRemaining($order->volRemaining)
            ->setState($order->orderState)
            ->setTypeId($order->typeID)
            ->setOrderRange($order->range)
            ->setAccountKey($order->accountKey)
            ->setDuration($order->duration)
            ->setEscrow($order->escrow)
            ->setPrice($order->price)
            ->setBid($order->bid)
            ->setIssued(new \DateTime($order->issued));

        return $marketOrder;

    }

    public function getClient(Corporation $corporation, $scope = 'corp'){
        $key = $corporation->getApiCredentials()[0];
        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
