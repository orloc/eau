<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\MarketOrder;
use AppBundle\Service\EBSDataMapper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class MarketOrderManager implements DataManagerInterface, MappableDataManagerInterface {

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

        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($corporation);

        if ($apiKey === null){
            throw new \Exception('No active api key for corp' . $corporation->getId() .' found');
        }

        $client = $this->getClient($apiKey);

        $orders = $client->MarketOrders();

        $marketOrders = $this->mapList($orders->orders, $corporation);

        return $marketOrders;

    }

    public function mapList($orders, array $options){
        $mappedOrders = [];
        $corp = $options['corp'] ? $options['corp'] : false;

        if ($corp instanceof Corporation){
            throw new \OptionDefinitionException(sprintf('Option corp required and must by of type %s', get_class(new Corporation())));
        }

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

            $mappedOrders[] = $order;
        }

        return $mappedOrders;
    }

    public function mapItem($order){
        $marketOrder = new MarketOrder();

        $marketOrder->setOrderId($order->orderID)
            ->setPlacedById($order->charID)
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

    public function getClient(ApiCredentials $key, $scope = 'corp'){
        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
