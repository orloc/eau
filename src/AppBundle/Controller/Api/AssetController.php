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
            ->getTopLevelAssetsByGroup($group);

        $paginator = $this->get('knp_paginator');

        $assets = $paginator->paginate($query,
            $request->query->get('page',1),
            $request->query->get('per_page', 2000)
        );

        $items = $assets->getItems();

        $itemTypes = $this->getRepository('EveBundle:ItemType', 'eve_data');
        $dataMapper = $this->get('app.datamapper.service');
        $eveDataRegistry = $this->get('evedata.registry');

        foreach ($items as $i){
            $updateData = $itemTypes->getItemTypeData($i->getTypeId());
            $flag = $eveDataRegistry->get('EveData:InvFlag')->getFlagName($i->getFlagId());

            $dataMapper->updateObject($i, array_merge($updateData, $flag));
        }

        $assets->setItems($items);

        $json = $this->get('serializer')->serialize($assets, 'json');

        return $this->jsonResponse($json);

    }

}
