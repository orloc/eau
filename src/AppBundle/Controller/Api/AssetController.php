<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\BuybackConfiguration;
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

        $assets = $this->paginateResult($request, $allItems);

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

        if ($corp instanceof $corp){
            foreach ($types as $t){
                $configs = $this->getDoctrine()->getRepository('AppBundle:BuybackConfiguration')
                    ->findConfigByType($corp, $t);

                if ($t === BuybackConfiguration::TYPE_REGION){

                    $regionPrices =
                }
            }
        }


        $priceManager = $this->get('app.price.manager');
        $items = $priceManager->updatePrices($items);

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
