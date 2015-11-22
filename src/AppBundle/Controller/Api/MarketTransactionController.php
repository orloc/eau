<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\Corporation;
use Carbon\Carbon;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Market transaction controller.
 */
class MarketTransactionController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{id}/account/{acc_id}/markettransaction", name="api.corporation.account.markettransactions", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @ParamConverter(name="account", class="AppBundle:Account", options={"id" = "acc_id"})
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp, Account $account)
    {
        $date = $request->get('date', null);
        $type = $request->get('type', 'buy');


        if ($date === null){
            $arr = $this->getDoctrine()->getRepository('AppBundle:MarketTransaction')->findBy([
                'account' => $account
            ]);
        } else {
            $dt = Carbon::createFromTimestamp($date);

            $repo = $this->getDoctrine()->getRepository('AppBundle:MarketTransaction');

            if ($type === 'buy'){
                $orders = $repo->getTotalBuyForDate($account, $dt);
            } else if ($type === 'sell'){
                $orders = $repo->getTotalSellForDate($account, $dt);
            } else {
                $orders = [];
            }

            $sorted = [];
            foreach ($orders as $o){
                if (!isset($sorted[$o->getItemName()])){
                    $sorted[$o->getItemName()] = [];
                }

                if (!isset($sorted[$o->getItemName()][$o->getPrice()])){
                    $sorted[$o->getItemName()][$o->getPrice()] = [];
                }

                $sorted[$o->getItemName()][$o->getPrice()][] = $o;
            }

            $reduced =  [];

            foreach ($sorted as $name => $prices){
                foreach ($prices as $price => $objs){
                    $obj = array_reduce($objs, function($carry, $value){
                        if ($carry === null){
                            return $value;
                        }

                        return $value->setQuantity($carry->getQuantity()+$value->getQuantity());
                    });

                    $reduced[] = $obj;
                }
            }
        }

        $json = $this->get('serializer')->serialize(isset($arr) ? $arr : $reduced, 'json');

        return $this->jsonResponse($json);

    }
}
