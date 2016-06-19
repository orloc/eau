<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;

use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\BuybackConfiguration;
use AppBundle\Entity\Corporation;
use JMS\SecurityExtraBundle\Annotation\Secure;
use AppBundle\Entity\AveragePrice;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class BuybackController
 * @package AppBundle\Controller\Api
 */
class BuybackController extends AbstractController implements ApiControllerInterface
{
    /**
     * Calculates buyback price from a paste
     * @Route("/industry/buyback", name="api.buyback_items", options={"expose"=true})
     * @Method("POST")
     * @Secure(roles="ROLE_USER")
     */
    public function getBuybackPrice(Request $request)
    {
        $data = $request->request->all();

        if (empty($data) || !isset($data[0]['name'])){
            return  $this->jsonResponse(json_encode(['code' => 400, 'message' => 'Empty Data or bad response keys']), 400);
        }

        $names = array_map(function ($d) { return $d['name']; }, $data);

        $eveReg = $this->get('evedata.registry');
        $priceManager = $this->get('app.price.manager');
        $items = $eveReg->get('EveBundle:ItemType')
            ->findTypesByName($names);

        $user = $this->getUser();

        $mainCharacter = $this->getRepository('AppBundle:Character')
            ->findOneBy(['user' => $user, 'is_main' => true]);

        $corp = $this->getRepository('AppBundle:Corporation')->findOneBy(
            ['eve_id' => $mainCharacter->getEveCorporationId()]
        );

        if ($corp instanceof $corp) {
            list($global, $specifics, $numberedPrices) = $this->determineDiscountTypeApplication($corp, $items);

            foreach ($items as $k => $i) {
                if (!isset($numberedPrices[$i['typeID']])){
                    continue;
                }
                
                $priceCall = $numberedPrices[$i['typeID']] instanceof AveragePrice
                    ? 'getAveragePrice'
                    : 'getAvgPrice';

                $prePrice = floatval($numberedPrices[$i['typeID']]->$priceCall());
                $items[$k]['price'] = $prePrice;

                if (!isset($specifics[$i['typeID']])) {
                    $items[$k]['new_price'] = $prePrice - ($prePrice * ($global / 100));
                } else {
                    $items[$k]['new_price'] = $specifics[$i['typeID']];
                }
            }
        } else {
            $items = $priceManager->updatePrices($items);
        }

        $json = $this->get('serializer')->serialize($items, 'json');

        return $this->jsonResponse($json);
    }

    protected function determineDiscountTypeApplication(Corporation $corp, array $items){
        $itemPriceRepo = $this->getRepository('AppBundle:ItemPrice');
        $avgPriceRepo = $this->getRepository('AppBundle:AveragePrice');

        $types = [
            BuybackConfiguration::TYPE_REGION,
            BuybackConfiguration::TYPE_GLOBAL,
            BuybackConfiguration::TYPE_SINGLE,
        ];

        $global = 0;
        $specifics = [];
        $numberedPrices = [];

        $itemIds = array_map(function ($i) {
            return intval($i['typeID']);
        }, $items);
        
        foreach ($types as $t) {
            $configs = $this->getDoctrine()->getRepository('AppBundle:BuybackConfiguration')
                ->findConfigByType($corp, $t);

            if (empty($configs)){
                continue;
            }

            if ($t === BuybackConfiguration::TYPE_REGION) {
                $prices = $itemPriceRepo->getItems(10000002, $itemIds);
            }

            if ($t === BuybackConfiguration::TYPE_GLOBAL) {
                $config = array_pop($configs);
                $global = abs(floatval($config->getBaseMarkDown()));
            }

            if ($t === BuybackConfiguration::TYPE_SINGLE) {
                foreach ($configs as $c) {
                    $specifics[$c->getSingleItem()] = [
                        'type_id' => $c->getSingleItem(),
                        'override' => $c->getOverride(),
                    ];
                }
            }
        }

        if (!isset($prices) || empty($prices)) {
            $prices = $avgPriceRepo->findInList($itemIds);
        }

        foreach ($prices as $p) {
            $numberedPrices[$p->getTypeId()] = $p;
        }

        return [ $global, $specifics, $numberedPrices];
    }
}

