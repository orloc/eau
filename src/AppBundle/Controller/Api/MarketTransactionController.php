<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\Corporation;
use Carbon\Carbon;
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
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp, Account $account)
    {
        $date = $request->get('date', null);
        $type = $request->get('type', 'buy');

        if ($date === null){
            $orders = $this->getDoctrine()->getRepository('AppBundle:MarketTransaction')->findBy([
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
        }

        $json = $this->get('serializer')->serialize($orders, 'json');

        return $this->jsonResponse($json);

    }
}
