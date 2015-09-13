<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\Corporation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Account controller.
 */
class AccountController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{id}/account", name="api.corporation.account", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     */
    public function indexAction(Corporation $corp)
    {

        $accounts = $this->getDoctrine()->getRepository('AppBundle:Account')
            ->findBy(['corporation' => $corp]);

        $balanceRepo = $this->getDoctrine()->getRepository('AppBundle:AccountBalance');
        foreach($accounts as $acc){
            $balance = $balanceRepo->getLatestBalance($acc)
                ->getBalance();

            $lastDay = ($b = $balanceRepo->getLastDayBalance($acc)) instanceof AccountBalance
                ? $b->getBalance()
                : 0;

            $acc->setCurrentBalance($balance)
                ->setLastDayBalance($lastDay);
        }

        $json = $this->get('serializer')->serialize($accounts, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/corporation/{id}/account/data", name="api.corporation.account_data", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     */
    public function dataAllAction(Corporation $corp){

        $accounts = $this->getDoctrine()->getRepository('AppBundle:Account')
            ->findBy(['corporation' => $corp]);

        $balanceRepo = $this->getDoctrine()->getRepository('AppBundle:AccountBalance');

        $accountData = [];
        foreach($accounts as $acc){
            $balances = $balanceRepo->getOrderedBalances($acc);

            foreach ($balances as $b){
                $accountData[] = [
                    'division' => $acc->getDivision(),
                    'date' => $b->getCreatedAt()->setTimezone(new \DateTimeZone('UTC'))
                        ->format("Y-m-d\Th:i:s\Z"),
                    'balance' => floatval($b->getBalance())
                ];
            }
        }

        $json = $this->get('serializer')->serialize($accountData, 'json');

        return $this->jsonResponse($json);

    }


}
