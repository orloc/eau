<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * MarketOrder controller.
 *
 */
class MarketOrderController extends AbstractController implements ApiControllerInterface
{

    /**
     * @Route("/corporation/{id}/marketorders", name="api.corporation.marketorders", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     */
    public function indexAction(Corporation $corp)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:MarketOrder');

        $orders = $repo->getOpenBuyOrders($corp);

        $sellorders = $repo->getOpenSellOrders($corp);

        $total_onMarket = array_reduce($sellorders, function($carry, $data){
            if ($carry === null){
                return ($data->getVolumeRemaining() * $data->getPrice());
            }

            return $carry + ($data->getVolumeRemaining() * $data->getPrice());
        });

        $total_escrow = array_reduce($orders, function($carry, $data){
            if($carry === null){
                return $data->getEscrow();
            }

            return $carry + $data->getEscrow();
        });

        $items = [
            'items' => $orders,
            'total_escrow' => $total_escrow,
            'total_on_market' => $total_onMarket
        ];

        $json = $this->get('jms_serializer')->serialize($items, 'json');

        return $this->jsonResponse($json);

    }
}
