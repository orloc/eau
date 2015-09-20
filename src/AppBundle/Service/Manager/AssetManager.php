<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\Corporation;
use AppBundle\Service\EBSDataMapper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AssetManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    private $pheal;

    private $registry;

    private $mapper;


    public function __construct(PhealFactory $pheal, EBSDataMapper $dataMapper, EveRegistry $registry, Registry $doctrine)
    {
        parent::construct($doctrine);
        $this->pheal = $pheal;
        $this->mapper = $dataMapper;
        $this->registry = $registry;
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

        if (!isset($options['group']) && ($grouping = $options['group']) instanceof AssetGroup){
            throw new \OptionDefinitionException(sprintf('Option corp required and must by of type %s', get_class(new AssetGroup())));
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

    public function getClient(ApiCredentials $key, $scope = 'corp'){

        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
