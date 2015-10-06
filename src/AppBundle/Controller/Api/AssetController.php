<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
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
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        $query = $this->getRepository('AppBundle:Asset')
            ->getAllByGroup($group);

        $allItems = $query->getResult();

        $assetManager = $this->get('app.asset.manager');

        $assetManager->updatePrices(
            $assetManager->updateResultSet($allItems)
        );

        $filteredList = array_filter($allItems, function($i) {
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


        $assets = $this->paginateResult($request, array_values($filteredList));

        $newList = [
            'total_price' => $total_price,
            'items' => $assets->getItems()
        ];

        $assets->setItems($newList);

        $json = $this->get('serializer')->serialize($assets, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/corporation/{id}/deliveries", name="api.corporation.deliveries", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     */
    public function deliveriesAction(Request $request, Corporation $corp)
    {

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        $query = $this->getRepository('AppBundle:Asset')
            ->getDeliveriesByGroup($group);


        $assetManager = $this->get('app.asset.manager');

        $items = $query->getResult();

        $assetManager->updatePrices(
            $assetManager->updateResultSet($items)
        );

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
     */
    public function getBuybackPrice(Request $request){

        $region_id = $request->query->get('region', null);
        $data = $request->request->all();

        $names = array_map(function($d){ return $d['name']; }, $data);

        $items = $this->get('evedata.registry')
            ->get('EveBundle:ItemType')
            ->findTypesByName($names);

        $assetManager = $this->get('app.asset.manager');
        $items = $assetManager->updatePrices($items);

        $json = $this->get('serializer')->serialize($items, 'json');

        return $this->jsonResponse($json);


        /*
        $ore_groups = $this->get('evedata.registry')->get('EveBundle:MarketGroup')
            ->getOreGroups();

        $item_repo = $this->get('evedata.registry')->get('EveBundle:ItemType') ;

        $oreTypes = [];
        foreach($ore_groups as $ore){
            $res = $item_repo->findTypesByGroupId($ore['marketGroupID']);
            $oreTypes[$ore['marketGroupName']] = $res;
        }


        $json = $this->get('serializer')->serialize($oreTypes, 'json');

        return $this->jsonResponse($json);
        */
    }


}
