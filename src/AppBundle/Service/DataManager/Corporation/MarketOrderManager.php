<?php

namespace AppBundle\Service\DataManager\Corporation;

use AppBundle\Entity\Corporation;
use AppBundle\Entity\MarketOrder;
use AppBundle\Entity\MarketOrderGroup;
use AppBundle\Exception\InvalidApiKeyException;
use AppBundle\Service\DataManager\AbstractManager;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\MappableDataManagerInterface;


class MarketOrderManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    public function getMarketOrders(Corporation $corporation){

        $apiKey = $this->getApiKey($corporation);

        $client = $this->getClient($apiKey);

        $orders = $client->MarketOrders([
            'characterID' => $apiKey->getEveCharacterId()
        ]);

        $marketOrderGroup = $this->mapList($orders->orders, [ 'corp' => $corporation ]);

        $corporation->addMarketOrderGroup($marketOrderGroup);

        return $marketOrderGroup;

    }

    public function mapList($orders, array $options){
        $marketGroup = new MarketOrderGroup();

        $corp = $options['corp'] ? $options['corp'] : false;

        if (!$corp instanceof Corporation){
            throw new \Exception(sprintf('Option corp required and must by of type %s', get_class(new Corporation())));
        }

        foreach ($orders as $o){
            $order = $this->mapItem($o);
            $marketGroup->addMarketOrder($order);

        }

        return $marketGroup;
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

    public static function getName(){
        return 'market_order_manager';
    }
}
