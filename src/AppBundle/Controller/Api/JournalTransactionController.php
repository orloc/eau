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
     * @Secure(roles="ROLE_CEO")
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
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function getByTypeAction(Request $request, Corporation $corp){
        $date = $request->get('date', null);

        $dt = $date === null
            ? Carbon::now()
            : Carbon::createFromTimestamp($date);

        $typeList = $this->getDoctrine()->getRepository('AppBundle:RefType')->getRefTypeIds();

        $typeIds =  array_map(function($d){
            return intval($d->getRefTypeId());
        }, $typeList);

        $transactions = $this->getDoctrine()->getRepository('AppBundle:JournalTransaction')
            ->getTransactionsByTypes($corp, $typeIds, $dt);

        $mapped = [];
        foreach ($typeList as $t){
            $mapped[$t->getRefTypeId()] = [
                'type' => $t,
                'trans' => []
            ];
        }

        foreach ($transactions as $trans) {
            $id = $trans->getRefTypeId();
            if (isset($mapped[$id])){
                array_push($mapped[$id]['trans'], $trans);
            }
        }
        foreach ($mapped as $i => $m){
            if (count($m['trans']) == 0){
                unset($mapped[$i]);
            }
        }

        $json = $this->get('jms_serializer')->serialize(array_values($mapped), 'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/corporation/{id}/journal_user_aggregate", name="api.corporation.journal.user_aggregate", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function getByUserAction(Request $request, Corporation $corp){

        $date = $request->get('date', null);
        $dt = $date === null
            ? Carbon::now()
            : Carbon::createFromTimestamp($date);

        $members = $this->getRepository('AppBundle:CorporationMember')
            ->findBy(['corporation' => $corp, 'disbanded_at' => null]);

        /**
         * @TODO Refactor this so we dont have N queries happening
         */
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
