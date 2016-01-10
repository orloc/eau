<?php

namespace AppBundle\Controller\Api\Eve;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Data controller.
 */
class DataController extends AbstractController implements ApiControllerInterface {

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

    /**
     * @Route("/market_groups", name="api.market_groups", options={"expose"=true})
     * @Method("GET")
     * @Secure(roles="ROLE_USER")
     */
    public function getTopLevelMarketGroups(Request $request){
        $items = $this->get('evedata.registry')
            ->get('EveBundle:MarketGroup')
            ->getTopLevelGroups();

        $json = json_encode($items);

        return $this->jsonResponse($json);
    }


}
