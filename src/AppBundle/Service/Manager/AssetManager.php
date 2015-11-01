<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\Corporation;
use AppBundle\Service\AssetDetailUpdateManager;
use AppBundle\Service\EBSDataMapper;
use AppBundle\Service\PriceUpdateManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AssetManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    protected $pheal;
    protected $item_manager;
    protected $price_manager;
    protected $mapper;


    public function __construct(PhealFactory $pheal, EveRegistry $registry, Registry $doctrine, AssetDetailUpdateManager $itemManager, PriceUpdateManager $priceManager)
    {
        parent::__construct($doctrine, $registry);
        $this->pheal = $pheal;
        $this->item_manager = $itemManager;
        $this->price_manager = $priceManager;
    }

    public function generateAssetList(Corporation $corporation){

        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($corporation);

        if ($apiKey === null){
            throw new \Exception('No active api key for corp' . $corporation->getId() .' found');
        }

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

    public function getClient(ApiCredentials $key, $scope = 'corp'){

        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
