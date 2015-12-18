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
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

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
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');
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

        $json = $this->get('jms_serializer')->serialize($transactions, 'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/corporation/{id}/journal_user_aggregate", name="api.corporation.journal.user_aggregate", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function getByUserAction(Request $request, Corporation $corp){
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $date = $request->get('date', null);
        $dt = $date === null
            ? Carbon::now()
            : Carbon::createFromTimestamp($date);

        $members = $this->getRepository('AppBundle:CorporationMember')
            ->findBy(['corporation' => $corp, 'disbanded_at' => null]);

        $memberIds =  array_map(function($d){
            return intval($d->getCharacterId());
        }, $members);

        $transactions = $this->getDoctrine()->getRepository('AppBundle:JournalTransaction')
            ->getTransactionsByMember($corp, $memberIds, $dt);

        foreach ($transactions as $k => $t){
            $u = $this->getRepository('AppBundle:CorporationMember')
                ->findOneBy(['character_id' => $t['user']]);
            $transactions[$k]['user'] = $u;
        }

        $json = $this->get('jms_serializer')->serialize($transactions, 'json');

        return $this->jsonResponse($json);
    }
}
