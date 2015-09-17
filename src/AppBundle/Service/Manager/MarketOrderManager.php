<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\Corporation;
use AppBundle\Entity\MarketOrder;
use AppBundle\Service\EBSDataMapper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Doctrine\ORM\EntityManager;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class MarketOrderManager {

    private $pheal;

    private $mapper;

    private $eve_registry;

    private $doctrine;

    public function __construct(PhealFactory $pheal, EBSDataMapper $dataMapper, EveRegistry $registry, Registry $doctrine){
        $this->pheal = $pheal;
        $this->mapper = $dataMapper;
        $this->eve_registry = $registry;
        $this->doctrine = $doctrine;
    }

    public function getMarketOrders(Corporation $corporation){

        $client = $this->getClient($corporation);

        $orders = $client->MarketOrders();

        $marketOrders = $this->mapList($orders->orders, $corporation);

        return $marketOrders;

    }

    private function mapList($orders, Corporation $corp){
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

    private function mapItem($order){
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



    private function getClient(Corporation $corporation, $scope = 'corp'){
        $key = $corporation->getApiCredentials();
        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
