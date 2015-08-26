<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
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

            $acc->setCurrentBalance($balance);
        }

        $json = $this->get('serializer')->serialize($accounts, 'json');

        return $this->jsonResponse($json);

    }
}
