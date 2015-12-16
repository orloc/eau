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

        $query = $this->getRepository('AppBundle:Asset')
            ->getAllByGroup($group);

        $allItems = $query->getResult();

        var_dump($allItems);die;
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
