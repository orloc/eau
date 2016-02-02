<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\BuybackConfiguration;
use AppBundle\Entity\Corporation;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Buyback Controller controller.
 */
class BuybackController extends AbstractController implements ApiControllerInterface {


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
                    $global = abs(floatval($config->getBaseMarkDown()));
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
                try {
                    $prePrice = floatval($numberedPrices[$i['typeID']]->getAvgPrice());
                    $items[$k]['price'] = $prePrice;
                    if (!isset($specifics[$i['typeID']]) ){
                        $items[$k]['new_price'] = $prePrice - ($prePrice * ($global / 100));
                    } else {
                        $items[$k]['new_price'] = $specifics[$i['typeID']];
                    }

                } catch (\Exception $e){

                }
            }
        } else {
            $items = $priceManager->updatePrices($items);
        }

        $json = $this->get('serializer')->serialize($items, 'json');

        return $this->jsonResponse($json);

    }
}
