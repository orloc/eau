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

    /**
     * @Route("/corporation/{id}/journal_type_aggregate", name="api.corporation.journal.aggregate", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     */
    public function getByTypeAction(Request $request, Corporation $corp){

        $date = $request->get('date', null);

        if ($date === null){
            $dt = Carbon::now();
        } else {
            $dt = Carbon::createFromTimestamp($date);
        }

        $types = $this->getRepository('AppBundle:RefType')
            ->findAll();

        $populatedTrans = [];
        foreach ($types as $t){
            $transactions = $this->getDoctrine()->getRepository('AppBundle:JournalTransaction')
                ->getTransactionsByType($corp, $t->getRefTypeId(), $dt);

            if (count($transactions)){
                $populatedTrans[] = [
                    'type' => $t, 'trans' => $transactions
                ];
            }
        }

        $json = $this->get('jms_serializer')->serialize($populatedTrans, 'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/corporation/{id}/journal_user_aggregate", name="api.corporation.journal.user_aggregate", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     */
    public function getByUserAction(Request $request, Corporation $corp){

        $date = $request->get('date', null);

        if ($date === null){
            $dt = Carbon::now();
        } else {
            $dt = Carbon::createFromTimestamp($date);
        }

        $members = $this->getRepository('AppBundle:CorporationMember')
            ->findBy(['corporation' => $corp, 'disbanded_at' => null]);

        $populatedTrans = [];
        foreach ($members as $m){
            $transactions = $this->getDoctrine()->getRepository('AppBundle:JournalTransaction')
                ->getTransactionsByMember($corp, $m, $dt);

            if (count($transactions)){
                $populatedTrans[] = [
                    'user' => $m, 'trans' => $transactions
                ];
            }
        }

        $json = $this->get('jms_serializer')->serialize($populatedTrans, 'json');

        return $this->jsonResponse($json);
    }

}
