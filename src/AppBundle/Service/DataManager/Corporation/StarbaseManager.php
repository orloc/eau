<?php

namespace AppBundle\Service\DataManager\Corporation;

use AppBundle\Entity\Corporation;
use AppBundle\Entity\Starbase;
use AppBundle\Service\AssetDetailUpdateManager;
use AppBundle\Service\PriceUpdateManager;
use Carbon\Carbon;
use AppBundle\Service\DataManager\MappableDataManagerInterface;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\AbstractManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Psr\Log\LoggerInterface;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class StarbaseManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface
{
    
    private $assetManager;
    private $priceManager;
    
    public function __construct(PhealFactory $pheal, Registry $doctrine,  EveRegistry $registry, LoggerInterface $logger, AssetDetailUpdateManager $itemManager, PriceUpdateManager $priceManager)
    {
        parent::__construct($pheal, $doctrine, $registry, $logger);
        $this->assetManager = $itemManager;
        $this->priceManager = $priceManager;
    }

    public function getStarbases(Corporation $c)
    {
        $apiKey = $this->getApiKey($c);
        $client = $this->getClient($apiKey);

        $bases = $client->StarbaseList()
            ->toArray();

        $this->mapList($bases['result']['starbases'], ['corp' => $c]);

        $starbases = $c->getStarbases();

        foreach ($starbases as $b) {
            try {
                $this->updateStarbaseDetail($b, $client);
            } catch (\Exception $e) {
                $this->log->error(sprintf('Error: %s on object %s of type %s', $e->getMessage(), $b->getItemId(), $b->getTypeId()));
            }
        }
    }

    public function updateStarbaseDetail(Starbase $base, $client)
    {
        $detail = $client->StarbaseDetail(['itemID' => floatval($base->getItemId())])
            ->toArray()['result'];

        $base->setGeneralSettings($detail['generalSettings'])
            ->setCombatSettings($detail['combatSettings'])
            ->setFuel($detail['fuel']);
    }

    public function mapList($items, array $options = [])
    {
        $corp = $options['corp'];
        $existing = $corp->getStarBases();

        $em = $this->doctrine->getManager();
        // remove the thing we dont want anymore
        if ($existing->count() !== count($items)) {
            $needsFlush = false;
            foreach ($existing as $e) {
                $found = false;
                foreach ($items as $i) {
                    if ($e->getItemId() == $i['itemID']) {
                        $found = true;
                    }
                }
                if (!$found) {
                    $needsFlush = true;
                    $em->remove($e);
                }
            }
            if ($needsFlush) {
                $em->flush();
            }
        }

        $repo = $em->getRepository('AppBundle:Starbase');

        foreach ($items as $i) {
            $exists = ($starbase = $repo->hasPOS($corp, $i['moonID'])) instanceof Starbase === true
            ? $starbase
            : null;
            $obj = $this->mapItem($i, $exists);

            if (!$exists instanceof Starbase) {
                $corp->addStarbase($obj);
            }

            $em->persist($obj);
        }
    }
    
    public function getUpdatedStarbaseList(Corporation $corp){
        $stations = $this->doctrine->getRepository('AppBundle:Starbase')
            ->findBy(['corporation' => $corp]);

        $typeRepo = $this->registry->get('EveBundle:ItemType');
        $attributeRepo = $this->registry->get('EveBundle:ItemAttribute');
        $locationRepo = $this->registry->get('EveBundle:MapDenormalize');
        $resourceRepo = $this->registry->get('EveBundle:ControlTowerResource');
        
        foreach ($stations as $s) {
            $attributeData = $attributeRepo->getItemAttributes($s->getTypeId());

            $ids = array_map(function ($i) {
                return intval($i['attributeID']);
            }, $attributeData);

            $attrDetails = $attributeRepo->getAttributes($ids);
            $fuelDetails = $resourceRepo->getFuelConsumption($s->getTypeId());
            $mergedData = [];
            foreach ($attributeData as $k => $d) {
                foreach ($attrDetails as $m) {
                    if ($d['attributeID'] === $m['attributeID']) {
                        $mergedData[] = array_merge($attributeData[$k], $m);
                    }
                }
            }
            
            $descriptors = array_merge(
                ['attributes' => $mergedData],
                $this->assetManager->determineLocationDetails($s->getLocationId()),
                $typeRepo->getItemTypeData($s->getTypeId()),
                [
                    'fuel' => is_array($s->getFuel())
                        ? array_map(function ($d) use ($typeRepo, $attributeRepo) {
                            $data = $typeRepo->getItemTypeData($d['typeID']);

                            return [
                                'type' => $data,
                                'typeID' => $d['typeID'],
                                'quantity' => $d['quantity'],
                            ];
                        }, $s->getFuel())
                        : [],
                    'moon' => $locationRepo->getLocationInfoById($s->getMoonId())
                ]
            );

            $fuels = $this->priceManager->updatePrices($descriptors['fuel']);

            $descriptors['fuel_consumption'] = $fuelDetails;
            $descriptors['fuel'] = $fuels;

            $s->setDescriptors($descriptors);
        }

        return $stations;
    }

    public function mapItem($item, Starbase $existing = null)
    {
        $obj = $existing === null ? new Starbase() : $existing;
        $obj->setItemId((int) $item['itemID'])
            ->setTypeId((int) $item['typeID'])
            ->setLocationId((int) $item['locationID'])
            ->setMoonId((int) $item['moonID'])
            ->setState((int) $item['state'])
            ->setStateTimestamp(Carbon::createFromTimestamp((int) $item['stateTimestamp']))
            ->setOnlineTimestamp(Carbon::createFromTimestamp((int) $item['onlineTimestamp']))
            ->setStandingOwnerId($item['standingOwnerID']);

        return $obj;
    }

    public static function getName()
    {
        return 'starbase_manager';
    }
}
