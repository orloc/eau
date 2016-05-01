<?php

namespace AppBundle\Service\DataManager\Corporation;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetGroup;
use AppBundle\Entity\Corporation;
use AppBundle\Exception\InvalidApiKeyException;
use AppBundle\Service\AssetDetailUpdateManager;
use AppBundle\Service\PriceUpdateManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EveBundle\Repository\Registry as EveRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;
use AppBundle\Service\DataManager\AbstractManager;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\MappableDataManagerInterface;

class AssetManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface
{
    protected $item_manager;
    protected $price_manager;

    public function __construct(PhealFactory $pheal, Registry $doctrine, EveRegistry $registry, LoggerInterface $logger, AssetDetailUpdateManager $itemManager, PriceUpdateManager $priceManager)
    {
        parent::__construct($pheal, $doctrine, $registry, $logger);
        $this->item_manager = $itemManager;
        $this->price_manager = $priceManager;
    }

    public function generateAssetList(Corporation $corporation)
    {
        try {
            $apiKey = $this->getApiKey($corporation);
        } catch (InvalidApiKeyException $e) {
            $this->log->info($e->getMessage());

            return;
        }

        $client = $this->getClient($apiKey);
        $result = $client->AssetList();

        $list = $result->assets;
        $grouping = new AssetGroup();
        $this->mapList($list, ['group' => $grouping]);
        $corporation->addAssetGroup($grouping);
    }

    public function mapList($assets, array $options)
    {
        $grouping = isset($options['group']) ? $options['group'] : false;

        if (!$grouping instanceof AssetGroup) {
            throw new OptionDefinitionException(sprintf('Option group required and must by of type %s', get_class(new AssetGroup())));
        }

        foreach ($assets as $asset) {
            $newAsset = $this->mapItem($asset);
            $grouping->addAsset($newAsset);

            if (isset($options['parent'])) {
                $options['parent']->addContent($newAsset);
            }

            if (isset($asset->contents)) {
                $this->mapList($asset->contents, ['group' => $grouping, 'parent' => $newAsset]);
            }
        }
    }

    public function mapItem($i)
    {
        $item = new Asset();

        $item->setFlagId($i->flag)
            ->setItemId($i->itemID)
            ->setQuantity($i->quantity)
            ->setSingleton($i->singleton)
            ->setTypeId($i->typeID);

        if (isset($i->locationID)) {
            $item->setLocationId($i->locationID);
        }

        return $item;
    }

    public function updateAssetGroupCache(array $corp_ids, $force = false)
    {
        $em = $this->doctrine->getManager();

        $agRepo = $this->doctrine->getRepository('AppBundle:AssetGroup');
        $aRepo = $this->doctrine->getRepository('AppBundle:Asset');

        foreach ($corp_ids as  $c){
            $this->log->info(sprintf('Updating Assets for Corp: %s', $c->getCorporationDetails() ? $c->getCorporationDetails()->getName()  : $c->getId()));
            $group = !$force === true
                ? $agRepo->getLatestNeedsUpdate($c)
                : $agRepo->getLatest($c);

            if (!$group){
                $this->log->warning('No updated needed');
                continue;
            }

            $allItems = $aRepo->getAllByGroup($group)->getResult();

            $start = microtime(true);
            $this->log->info(sprintf('Updating Location and Price data'));
            $updatedItems = $this->price_manager->updatePrices(
                $this->item_manager->updateDetails($allItems)
            );
            $this->log->info(sprintf('Done in %s', microtime(true) - $start));

            $filteredList = array_filter($updatedItems, function ($i) {
                if (!isset($i->getDescriptors()['name'])) {
                    return false;
                }
                $name = $i->getDescriptors()['name'];
                $t = strstr($name, 'Blueprint');
                return $t === false;
            });

            $this->log->info(sprintf('Blueprints removed from calculation list'));
            $this->log->info(sprintf('Computing Totals'));
            $total_price = array_reduce($filteredList, function ($carry, $data) {
                if ($carry === null) {
                    return $data->getDescriptors()['total_price'];
                }

                return $carry + $data->getDescriptors()['total_price'];
            });
            $group->setAssetSum($total_price)
                ->setHasBeenUpdated(true);

            $this->log->info(sprintf('Done Rolling up totals with price: %s for %s', $total_price, $c->getCorporationDetails()->getName()));

            $em->persist($group);
            $em->flush();
        }
    }

    public function flattenAssets(Asset $asset)
    {
        $list = [$asset];
        $this->helperFlatten($asset->getContents()->toArray(), $list);

        return $list;
    }

    protected function helperFlatten(array $nodes, array &$list)
    {
        foreach ($nodes as $a) {
            $list[] = $a;
            if (!count($a->getContents())) {
                continue;
            } else {
                return $this->helperFlatten($a->getContents()->toArray(), $list);
            }
        }
    }

    public static function getName()
    {
        return 'asset_manager';
    }
}
