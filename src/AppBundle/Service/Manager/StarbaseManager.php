<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class StarbaseManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface
{

    public function getStarbases(Corporation $c){

        $apiKey = $this->getApiKey($c);
        $client = $this->getClient($apiKey);

        /*
        $bases = $client->StarbaseList();

        $this->mapList($bases);
        */
    }

    public function mapList($items, array $options = []){

        var_dump($items);

        die;
    }

    public function mapItem($item){

    }

    public static function getName(){
        return 'starbase_manager';
    }

}
