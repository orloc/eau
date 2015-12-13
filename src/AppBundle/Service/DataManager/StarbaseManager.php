<?php

namespace AppBundle\Service\DataManager;

use AppBundle\Entity\Corporation;
use AppBundle\Entity\Starbase;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Registry;

class StarbaseManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface
{

    public function getStarbases(Corporation $c){
        $apiKey = $this->getApiKey($c);
        $client = $this->getClient($apiKey);

        $bases = $client->StarbaseList()
            ->toArray();

        $this->mapList($bases['result']['starbases'], [ 'corp' => $c]);

        $starbases = $c->getStarbases();

        foreach ($starbases as $b){
            try {
                $this->updateStarbaseDetail($b, $client);
            } catch (\Exception $e){
                $this->log->error(sprintf("Error: %s on object %s of type %s",$e->getMessage(), $b->getItemId(), $b->getTypeId()));
            }
        }
    }

    public function updateStarbaseDetail(Starbase $base, $client){

        $detail = $client->StarbaseDetail(['itemID' => floatval($base->getItemId())])
            ->toArray()['result'];

        $base->setGeneralSettings($detail['generalSettings'])
            ->setCombatSettings($detail['combatSettings'])
            ->setFuel($detail['fuel']);

    }

    public function mapList($items, array $options = []){

        $corp = $options['corp'];
        $em = $this->doctrine->getManager();

        $repo = $em->getRepository('AppBundle:Starbase');

        foreach ($items as $i){
            $exists = $repo->hasPOS($corp, $i['moonID']);
            $obj = $this->mapItem($i, $exists instanceof Starbase ? $exists : false);

            if (!$exists instanceof Starbase){
                $corp->addStarbase($obj);
            }

            $em->persist($obj);
        }

    }

    public function mapItem($item, Starbase $existing = null){

        $obj = !$existing ? new Starbase() : $existing;
        $obj->setItemId((int)$item['itemID'])
            ->setTypeId((int)$item['typeID'])
            ->setLocationId((int)$item['locationID'])
            ->setMoonId((int)$item['moonID'])
            ->setState((int)$item['state'])
            ->setStateTimestamp(Carbon::createFromTimestamp((int)$item['stateTimestamp']))
            ->setOnlineTimestamp(Carbon::createFromTimestamp((int)$item['onlineTimestamp']))
            ->setStandingOwnerId($item['standingOwnerID']);

        return $obj;
    }

    public static function getName(){
        return 'starbase_manager';
    }

}
