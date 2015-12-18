<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
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
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $date = $request->get('date', null);
        $type = $request->get('type', 'buy');

        if ($date === null){
            $arr = $this->getDoctrine()->getRepository('AppBundle:MarketTransaction')->findBy([
                'account' => $account
            ]);
        } else {
            $reduced = $this->reduceOrders(
                $this->getOrderReference(
                    $this->getOrders($account, $date, $type)
                )
            );
        }

        $json = $this->get('serializer')->serialize(isset($arr) ? $arr : $reduced, 'json');

        return $this->jsonResponse($json);

    }

    protected function reduceOrders(array $orders){
        $reduced =  [];
        foreach ($orders as $name => $prices){
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
        return $reduced;
    }

    protected function getOrderReference(array $orders){
        $sorted = [];
        foreach ($orders as $o){
            $itemName = $o->getItemName();
            $itemPrice = $o->getPrice();
            if (!isset($sorted[$itemName])){
                $sorted[$itemName] = [];
            }

            if (!isset($sorted[$itemName][$itemPrice])){
                $sorted[$itemName][$itemPrice] = [];
            }
            $sorted[$itemName][$itemPrice][] = $o;
        }

        return $sorted;
    }

    protected function getOrders(Account $account, $date, $type){
        $dt = Carbon::createFromTimestamp($date);
        $orders = [];
        $repo = $this->getDoctrine()->getRepository('AppBundle:MarketTransaction');

        if ($type === 'buy'){
            $orders = $repo->getTotalBuyForDate($account, $dt);
        } else if ($type === 'sell'){
            $orders = $repo->getTotalSellForDate($account, $dt);
        }

        return $orders;
    }
}
