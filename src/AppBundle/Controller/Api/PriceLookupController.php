<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Price Lookup controller.
 */
class PriceLookupController extends AbstractController implements ApiControllerInterface
{
    /**
     * @Route("/industry/price_lookup", name="api.price_lookup", options={"expose"=true})
     * @Method("POST")
     * @Secure(roles="ROLE_USER")
     */
    public function getPriceDistributionAction(Request $request)
    {
        $regions = $request->request->get('regions', false);
        $items = $request->request->get('items', false);

        $authChecker = $this->get('security.authorization_checker');
        if ($authChecker->isGranted('ROLE_DIRECTOR')) {
            $em = $this->getDoctrine()->getManager();
            $main = $em->getRepository('AppBundle:Character')->getMainCharacter($this->getUser());
            if ($main !== null) {
                $corporation = $this->getDoctrine()->getRepository('AppBundle:Corporation')
                    ->findByCorpName($main->getCorporationName());

                $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corporation, 'Unauthorized access!');
            }
        }

        if (!(bool) $items || !(bool) $regions) {
            return $this->jsonResponse(json_encode(['error' => 'invalid']), 400);
        }

        $reg = $this->get('evedata.registry');
        $doctrine = $this->getDoctrine();

        $real_items = $reg->get('EveBundle:ItemType')
            ->findAllByTypes($items);

        // update the item prices per region
        $pRef = [];
        foreach ($regions as $r) {
            $pRef[$r] = [];
            if ($r === 0) {
                $item_prices = array_map(function ($p) {
                    return [
                        'avg_price' => $p->getAveragePrice(),
                        'type_id' => $p->getTypeId(),
                    ];
                }, $doctrine->getRepository('AppBundle:AveragePrice')->findInList($items));
            } else {
                $item_prices = array_map(function ($p) {
                    return [
                        'low_price' => $p->getLowPrice(),
                        'high_price' => $p->getHighPrice(),
                        'avg_price' => $p->getHighPrice(),
                        'type_id' => $p->getTypeId(),
                        'order_count' => $p->getOrderCount(),
                        'volume' => $p->getVolume(),
                    ];
                }, $doctrine->getRepository('AppBundle:ItemPrice')->getItems($r, $items));
            }
            foreach ($item_prices as $p) {
                $pRef[$r][$p['type_id']] = $p;
            }
        }

        // try and find a journal buy and / or sell record for this item
        $marketingRepo = $doctrine->getRepository('AppBundle:MarketTransaction');
        $retItems = [];
        foreach ($real_items as $i) {
            $data = [
                'item' => $i,
            ];
            foreach ($pRef as $k => $r) {
                $data[$k] = $r;
            }

            if (isset($corporation) && $corporation instanceof Corporation) {
                $data['last_buy'] = $marketingRepo->findLatestTransactionByItemType($corporation, 'buy', $i['typeID']);
                $data['last_sell'] = $marketingRepo->findLatestTransactionByItemType($corporation, 'sell', $i['typeID']);
            }

            $retItems[] = $data;
        }

        return $this->jsonResponse($this->get('jms_serializer')->serialize($retItems, 'json'));
    }
}
