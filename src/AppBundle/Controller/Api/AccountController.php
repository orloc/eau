<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\Corporation;
use Carbon\Carbon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/corporation/account/{id}/data", name="api.corporation.account_data", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Account")
     * @Method("GET")
     */
    public function dataAllAction(Request $request, Account $account){

        $date = $request->get('date', null);

        $balanceRepo = $this->getDoctrine()->getRepository('AppBundle:AccountBalance');

        $accountData = [];

        if (null === $date){
            $balances = $balanceRepo->getOrderedBalances($account);
        } else {
            $dateTime = Carbon::createFromTimestamp($date);

            $balances = $balanceRepo->getOrderedBalancesByDate($account, $dateTime);
        }

        foreach ($balances as $b){
            $accountData[] = [
                'division' => $account->getDivision(),
                'date' => $b->getCreatedAt()->setTimezone(new \DateTimeZone('UTC'))
                    ->format("Y-m-d\Th:i:s\Z"),
                'balance' => floatval($b->getBalance())
            ];
        }

        $json = $this->get('serializer')->serialize($accountData, 'json');

        return $this->jsonResponse($json);

    }


}
