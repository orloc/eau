<?php

namespace AppBundle\Service\DataManager;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\Corporation;
use AppBundle\Service\AssetDetailUpdateManager;
use AppBundle\Service\PriceUpdateManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AssetManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    protected $item_manager;
    protected $price_manager;


    public function __construct(PhealFactory $pheal, Registry $doctrine, EveRegistry $registry, AssetDetailUpdateManager $itemManager, PriceUpdateManager $priceManager)
    {
        parent::__construct($pheal, $doctrine, $registry);
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

    public function updateAssetGroupCache(Corporation $corp){
        $group = $this->doctrine->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        if (!$group->getHasBeenUpdated()){
            $query = $this->doctrine->getRepository('AppBundle:Asset')
                ->getAllByGroup($group);

            $allItems = $query->getResult();

            $updatedItems = $this->price_manager->updatePrices(
                $this->item_manager->updateDetails($allItems)
            );

            $filteredList = array_filter($updatedItems, function($i) {
                if (!isset($i->getDescriptors()['name'])) {
                    return false;
                }

                $name = $i->getDescriptors()['name'];
                $t = strstr($name, 'Blueprint');

                return $t === false;
            });

            $total_price = array_reduce($filteredList, function($carry, $data){
                if ($carry === null){
                    return $data->getDescriptors()['total_price'];
                }

                return $carry + $data->getDescriptors()['total_price'];
            });

            $group->setAssetSum($total_price)
                ->setHasBeenUpdated(true);

            $em = $this->doctrine->getManager();
            $em->persist($group);

            $em->flush();
        }
    }

    public static function getName(){
        return 'asset_manager';
    }
}
