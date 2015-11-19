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

class JournalTransactionController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{id}/account/{acc_id}/journal_transactions", name="api.corporation.account.journaltransactions", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @ParamConverter(name="account", class="AppBundle:Account", options={"id" = "acc_id"})
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp, Account $account)
    {
        $date = $request->get('date', null);

        if ($date === null){
            $dt = Carbon::now();
        } else {
            $dt = Carbon::createFromTimestamp($date);
        }

        $transactions = $this->getDoctrine()->getRepository('AppBundle:JournalTransaction')
            ->getTransactionsByAccount($account, $dt);

        $json = $this->get('serializer')->serialize($transactions, 'json');

        return $this->jsonResponse($json);

    }
}
