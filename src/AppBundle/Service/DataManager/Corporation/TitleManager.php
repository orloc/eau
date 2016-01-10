<?php

namespace AppBundle\Service\DataManager\Corporation;

use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\Corporation;
use AppBundle\Service\DataManager\AbstractManager;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\MappableDataManagerInterface;

class TitleManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    public function updateTitles(Corporation $corporation){

        $apiKey = $this->getApiKey($corporation);

        $client = $this->getClient($apiKey);

        $titles = $client->Titles([
            'characterID' => $apiKey->getEveCharacterId()
        ]);

        $this->mapList($titles->titles, ['corp' => $corporation]);

    }

    public function mapList($items, array $options){
        foreach ($items as $i){
            var_dump($i);die;
        }
    }

    public function mapItem($item){
        $balance = new AccountBalance();
        $balance->setBalance($item->balance);

        return $balance;

    }

    public static function getName(){
        return 'title_manager';
    }

}
