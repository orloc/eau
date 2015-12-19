<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\BuybackConfiguration;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Account controller.
 */
class AssetController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{id}/assets", name="api.corporation.assets", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        $query = $this->getRepository('AppBundle:Asset')
            ->getAllByGroup($group);

        $assets = $this->paginateResult($request, $query);

        $newList = [
            'total_price' => $group->getAssetSum(),
            'items' => $assets->getItems()
        ];

        $assets->setItems($newList);

        $json = $this->get('serializer')->serialize($assets, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/corporation/{id}/asset_summary", name="api.corporation.assets.summary", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function summaryAction(Request $request, Corporation $corp){
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        $results = $this->getRepository('AppBundle:Asset')->getAssetItemSummary($group);

        $json = json_encode($results);

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/corporation/{id}/assets/clustered", name="api.corporation.assets.clustered", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function getAssetsOrganizedAction(Request $request, Corporation $corp){
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $sort = $request->query->get('sort', false);

        if (!$sort || !in_array($sort, ['location', 'category'])){
            return $this->jsonResponse(json_encode(['error' => 'invalid sort']), 400);
        }

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);
        $repo = $this->getRepository('AppBundle:Asset');


        switch ($sort){
            case 'location':
                $results = $repo->getLocationsByAssetGroup($group);
                $locations = [];
                $updatedResults = $this->get('app.itemdetail.manager')
                    ->updateDetails($results);

                foreach ($updatedResults as $r){
                    if ($r->getLocationId() === null) { continue;}

                    $desc = $r->getDescriptors();
                    $name =  $desc['stationName'] === null
                        ? 'In Space @ '.$desc['system']
                        : $desc['stationName'];

                    $locations[$r->getLocationId()] = [
                        'name' => $name,
                        'id' => $r->getLocationId()
                    ];
                }

                return $this->jsonResponse(json_encode(array_values($locations)), 200);

                break;
            case 'category':
                $itemIds = array_map(function($i) {
                    return $i['typeId'];
                },$repo->getTypeIDSByAssetGroup($group));

                $reg = $this->get('evedata.registry');

                $types = $reg->get('EveBundle:ItemType')
                    ->findAllByTypes($itemIds);

                $map = $this->doMap($types);
                return $this->jsonResponse(json_encode(array_values($map)), 200);
                break;
        }
    }

    protected function doMap(array $types){
        $no_parents = false;
        $reg = $this->get('evedata.registry');
        while(!$no_parents){
            $ref =[];
            $marketGroupIds = array_unique(array_map(function($t) use (&$ref){
                $refPoint = isset($t['typeID']) ? $t['marketGroupID'] : $t['parentGroupID'];
                if (!isset($ref[$refPoint])){
                    $ref[$refPoint][] = $t;
                }
                $ref[$refPoint][] = $t;
                return $refPoint;
            }, $types));

            $types = $reg->get('EveBundle:MarketGroup')->getInList($marketGroupIds);

            foreach ($types as $k => $t){
                $types[$k]['children'] =  $ref[$t['marketGroupID']];
            }

            $no_parents = empty(array_filter($types, function($t){
                return $t['parentGroupID'] !== null;
            }));
        }

        return $types;
    }

    /**
     * @Route("/corporation/{id}/location_assets", name="api.corporation.location_assets", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function locationAssetsAction(Request $request, Corporation $corp){
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');
        $loc = $request->query->get('location', false);
        if (!$loc){
            return $this->jsonResponse(json_encode(['error' => 'invalid']), 400);
        }

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        $repo = $this->getRepository('AppBundle:Asset');
        $assets = $repo->getAssetsByLocation($group, $loc);

        $priceManager = $this->get('app.price.manager');

        $updatedItems = $this->get('app.itemdetail.manager')->updateDetails($assets);
        $priceManager->updatePrices($updatedItems);

        $json =  $this->get('jms_serializer')->serialize($updatedItems, 'json');
        return $this->jsonResponse($json, 200);

    }

    /**
     * @Route("/corporation/{id}/deliveries", name="api.corporation.deliveries", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function deliveriesAction(Request $request, Corporation $corp)
    {
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        $query = $this->getRepository('AppBundle:Asset')
            ->getDeliveriesByGroup($group);


        $items = $query->getResult();

        $priceManager = $this->get('app.price.manager');

        $updatedItems = $this->get('app.itemdetail.manager')->updateDetails($items);
        $priceManager->updatePrices($updatedItems);

        $total_price = array_reduce($items, function($carry, $data){
            if ($carry === null){
                return $data->getDescriptors()['total_price'];
            }

            return $carry + $data->getDescriptors()['total_price'];
        });

        $newList = [
            'total_price' => $total_price,
            'items' => array_values($items)
        ];

        $json = $this->get('serializer')->serialize($newList, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/industry/buyback", name="api.buyback_items", options={"expose"=true})
     * @Method("POST")
     * @Secure(roles="ROLE_USER")
     */
    public function getBuybackPrice(Request $request){

        $data = $request->request->all();

        $names = array_map(function($d){ return $d['name']; }, $data);

        $eveReg = $this->get('evedata.registry');
        $priceManager = $this->get('app.price.manager');
        $itemPriceRepo = $this->getRepository('EveBundle:ItemPrice', 'eve_data');
        $items = $eveReg->get('EveBundle:ItemType')
            ->findTypesByName($names);

        $types = [
            BuybackConfiguration::TYPE_REGION,
            BuybackConfiguration::TYPE_GLOBAL,
            BuybackConfiguration::TYPE_SINGLE,
        ];

        $user = $this->getUser();

        $mainCharacter = $this->getRepository('AppBundle:Character')
            ->findOneBy(['user' => $user, 'is_main' => true]);

        $corp = $this->getRepository('AppBundle:Corporation')->findOneBy(
            ['eve_id' => $mainCharacter->getEveCorporationId() ]
        );


        $itemIds = array_map(function($i){
            return intval($i['typeID']);
        }, $items);


        if ($corp instanceof $corp){

            $regionPrices = [];
            $global = 0;
            $specifics = [];

            foreach ($types as $t){
                $configs = $this->getDoctrine()->getRepository('AppBundle:BuybackConfiguration')
                    ->findConfigByType($corp, $t);

                if ($t === BuybackConfiguration::TYPE_REGION){
                    $regionPrices = $itemPriceRepo->getItems(10000002, $itemIds);
                }

                if ($t === BuybackConfiguration::TYPE_GLOBAL){
                    $config = array_pop($configs);
                    $global = $config->getBaseMarkDown();
                }

                if ($t === BuybackConfiguration::TYPE_SINGLE){
                    foreach ($configs as $c){
                        $specifics[$c->getSingleItem()] = [
                            'type_id' => $c->getSingleItem(),
                            'override' => $c->getOverride()
                        ];
                    }
                }
            }

            $numberedPrices = [];
            foreach ($regionPrices as $p){
                $numberedPrices[$p->getTypeId()] = $p;
            }
            unset($regionPrices);

            foreach ($items as $k => $i){
                if (!isset($specifics[$i['typeID']]) ){
                    $prePrice = floatval($numberedPrices[$i['typeID']]->getAvgPrice());
                    $items[$k]['price'] = $prePrice - ($prePrice * ($global / 100));
                } else {
                    $items[$k]['price'] = $specifics[$i['typeID']];
                }
            }
        } else {
            $items = $priceManager->updatePrices($items);
        }

        $json = $this->get('serializer')->serialize($items, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/industry/{id}/price_lookup", name="api.price_lookup", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("POST")
     * @Secure(roles="ROLE_USER")
     */
    public function getPriceDistributionAction(Request $request, Corporation $corp){

        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $regions = $request->request->get('regions', false);
        $items = $request->request->get('items', false);

        if (!(bool)$items || !(bool)$regions ){
            return $this->jsonResponse(json_encode(['error' => 'invalid']), 400);
        }

        $reg = $this->get('evedata.registry');
        $doctrine = $this->getDoctrine();

        $real_items = $reg->get('EveBundle:ItemType')
            ->findAllByTypes($items);

        // update the item prices per region
        $pRef = [];
        foreach ($regions as $r){
            $pRef[$r] =[];
            if ($r === 0){
                $item_prices = array_map(function($p){
                    return [
                        'avg_price' => $p->getAveragePrice(),
                        'type_id' => $p->getTypeId()
                    ];
                }, $doctrine->getRepository('EveBundle:AveragePrice', 'eve_data')->findInList($items));
            } else {
                $item_prices = array_map(function($p){
                    return [
                        'low_price' => $p->getLowPrice(),
                        'high_price' => $p->getHighPrice(),
                        'avg_price' => $p->getHighPrice(),
                        'type_id' => $p->getTypeId(),
                        'order_count' => $p->getOrderCount(),
                        'volume' => $p->getVolume()
                    ];
                }, $doctrine->getRepository('EveBundle:ItemPrice', 'eve_data')->getItems($r, $items));
            }
            foreach ($item_prices as $p){
                $pRef[$r][$p['type_id']] = $p;
            }
        }

        // try and find a journal buy and / or sell record for this item
        $marketingRepo = $doctrine->getRepository('AppBundle:MarketTransaction');
        $retItems =[];
        foreach ($real_items as $i){
            $data = [
                'item' => $i
            ];
            foreach ($pRef as $k => $r){
                $data[$k] = $r;
            }

            $data['last_buy'] = $marketingRepo->findLatestTransactionByItemType($corp, 'buy',$i['typeID']);
            $data['last_sell'] = $marketingRepo->findLatestTransactionByItemType($corp, 'sell',$i['typeID']);

            $retItems[] = $data;
        }

        return $this->jsonResponse($this->get('jms_serializer')->serialize($retItems,'json'));
    }

    /**
     * @Route("/item_list", name="api.item_list", options={"expose"=true})
     * @Method("GET")
     * @Secure(roles="ROLE_CEO")
     */
    public function getItemList(Request $request){

        $items = $this->get('evedata.registry')
            ->get('EveBundle:ItemType')
            ->findAllMarketItems();

        $json = json_encode($items);

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/market_groups", name="api.market_groups", options={"expose"=true})
     * @Method("GET")
     * @Secure(roles="ROLE_CEO")
     */
    public function getTopLevelMarketGroups(Request $request){
        $items = $this->get('evedata.registry')
            ->get('EveBundle:MarketGroup')
            ->getTopLevelGroups();

        $json = json_encode($items);

        return $this->jsonResponse($json);
    }


}
