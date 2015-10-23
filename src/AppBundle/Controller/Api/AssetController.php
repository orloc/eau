<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
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
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        $query = $this->getRepository('AppBundle:Asset')
            ->getAllByGroup($group);

        $allItems = $query->getResult();


        if (!$group->getHasBeenUpdated()){
            $updatedItems = $this->get('app.itemdetail.manager')->updateDetails($allItems);

            $priceManager = $this->get('app.price.manager');
            $priceManager->updatePrices($updatedItems);

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

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);

            $em->flush();
        } else {
            $filteredList = $allItems;
        }


        $assets = $this->paginateResult($request, array_values($filteredList));

        $newList = [
            'total_price' => $group->getAssetSum(),
            'items' => $assets->getItems()
        ];

        $assets->setItems($newList);

        $json = $this->get('serializer')->serialize($assets, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/corporation/{id}/deliveries", name="api.corporation.deliveries", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     */
    public function deliveriesAction(Request $request, Corporation $corp)
    {

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
        $items = $eveReg->get('EveBundle:ItemType')
            ->findTypesByName($names);

        $configs = $this->getDoctrine()->getRepository('AppBundle:BuybackConfiguration')
            ->findAll();

        $user = $this->getUser();

        $priceManager = $this->get('app.price.manager');
        $priceManager->updatePrices($items);

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

    /**
     * @Route("/item_list", name="api.item_list", options={"expose"=true})
     * @Method("GET")
     * @Secure(roles="ROLE_USER")
     */
    public function getItemList(Request $request){

        $items = $this->get('evedata.registry')
            ->get('EveBundle:ItemType')
            ->findAllMarketItems();

        $json = json_encode($items);

        return $this->jsonResponse($json);

    }


}
