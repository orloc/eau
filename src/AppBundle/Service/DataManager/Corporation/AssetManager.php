<?php

namespace AppBundle\Service\DataManager\Corporation;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\Corporation;
use AppBundle\Service\AssetDetailUpdateManager;
use AppBundle\Service\PriceUpdateManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;
use AppBundle\Service\DataManager\AbstractManager;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\MappableDataManagerInterface;

class AssetManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    protected $item_manager;
    protected $price_manager;


    public function __construct(PhealFactory $pheal, Registry $doctrine, EveRegistry $registry, LoggerInterface $logger, AssetDetailUpdateManager $itemManager, PriceUpdateManager $priceManager)
    {
        parent::__construct($pheal, $doctrine, $registry, $logger);
        $this->item_manager = $itemManager;
        $this->price_manager = $priceManager;
    }

    public function generateAssetList(Corporation $corporation){

        $apiKey = $this->getApiKey($corporation);

        $client = $this->getClient($apiKey);
        $result = $client->AssetList();

        $list = $result->assets;
        $grouping = new AssetGroup();
        $this->mapList($list, [ 'group' => $grouping ]);
        $corporation->addAssetGroup($grouping);

    }

    public function mapList($assets, array $options){
        $grouping = isset($options['group']) ? $options['group'] : false;

        if (!$grouping instanceof AssetGroup){
            throw new OptionDefinitionException(sprintf('Option group required and must by of type %s', get_class(new AssetGroup())));
        }

        foreach ($assets as $asset){
            $newAsset = $this->mapItem($asset);
            $grouping->addAsset($newAsset);

            if (isset($options['parent'])) {
                $options['parent']->addContent($newAsset);
            }

            if (isset($asset->contents)){
                $this->mapList($asset->contents, [ 'group' => $grouping, 'parent' =>  $newAsset]) ;
            }
        }
    }

    public function mapItem($i){
        $item = new Asset();

        $item->setFlagId($i->flag)
            ->setItemId($i->itemID)
            ->setQuantity($i->quantity)
            ->setSingleton($i->singleton)
            ->setTypeId($i->typeID);


        if (isset($i->locationID)){
            $item->setLocationId($i->locationID);
        }

        return $item;
    }

    public function updateAssetGroupCache(array $corp_ids){
        $start = microtime(true);
        $groups = $this->doctrine->getRepository('AppBundle:AssetGroup')
            ->getLatestNeedsUpdateAssetGroupByIds($corp_ids);

        $allItems = $this->doctrine->getRepository('AppBundle:Asset')
            ->getAllByGroups($groups);

        $updatedItems = $this->price_manager->updatePrices(
            $this->item_manager->updateDetails($allItems)
        );

        $this->log->info(sprintf("Done updating assets in %s", microtime(true) - $start));
        $start = microtime(true);
        $filteredList = array_filter($updatedItems, function($i) {
            if (!isset($i->getDescriptors()['name'])) {
                return false;
            }

            $name = $i->getDescriptors()['name'];
            $t = strstr($name, 'Blueprint');

            return $t === false;
        });

        $this->log->info(sprintf("Blueprints removed from calculation list in %s", microtime(true) - $start));
        $em = $this->doctrine->getManager();
        $start = microtime(true);
        foreach ($groups as $g){
            $total_price = array_reduce($filteredList, function($carry, $data){
                if ($carry === null){
                    return $data->getDescriptors()['total_price'];
                }
                return $carry + $data->getDescriptors()['total_price'];
            });
            $g->setAssetSum($total_price)
                ->setHasBeenUpdated(true);

            $em->persist($g);
        }
        $this->log->info(sprintf("Done with price cleanup in %s", microtime(true) - $start));

        $em->flush();
    }

    public function flattenAssets(Asset $asset){
        $list = [$asset];
        $this->helperFlatten($asset->getContents()->toArray(), $list);
        return $list;
    }

    protected function helperFlatten(array $nodes, array &$list)
    {
        foreach ($nodes as $a){
            $list[] = $a;
            if (!count($a->getContents())) {
                continue;
            } else {
                return $this->helperFlatten($a->getContents()->toArray(), $list);
            }
        }
    }

    public static function getName(){
        return 'asset_manager';
    }
}
