<?php

namespace AppBundle\Controller\Api\Corporation;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Asset controller.
 */
class AssetController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/{id}/assets", name="api.corporation.assets", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_DIRECTOR")
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
     * @Route("/{id}/asset_summary", name="api.corporation.assets.summary", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_DIRECTOR")
     * @Method("GET")
     */
    public function summaryAction(Request $request, Corporation $corp){
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $group = $this->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);

        $query = $this->getRepository('AppBundle:Asset')->getAssetItemSummary($group);

        $assets = $this->paginateResult($request, $query);

        $json = $this->get('serializer')->serialize($assets,'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/{id}/assets/clustered", name="api.corporation.assets.clustered", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_DIRECTOR")
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
     * @Route("/{id}/location_assets", name="api.corporation.location_assets", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_DIRECTOR")
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
     * @Route("/{id}/deliveries", name="api.corporation.deliveries", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_DIRECTOR")
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
}
