<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\Starbase;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

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
            $this->updateStarbaseDetail($b, $client);
        }
    }

    public function updateStarbaseDetail(Starbase $base, $client){

        $detail = $client->StarbaseDetail(['itemID' => (int)$base->getItemId()])
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
            if (!($exists = $repo->hasPOS($corp, $i['moonID'])) instanceof Starbase){
                $obj = $this->mapItem($i);
            }

            if (isset($obj)){
                $corp->addStarbase($obj);
                $exists = $obj;
            }

            if ($exists->getState() !== (int)$i['state']){
                $exists->setState((int)$i['state']);
                $em->persist($exists);
            }
        }

    }

    public function mapItem($item){

        $obj = new Starbase();
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
