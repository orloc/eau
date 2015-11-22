<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndustrialController extends Controller
{
    /**
     * @Route("/industry", name="industry.buyback")
     * @Method("GET")
     * @Secure(roles="ROLE_CORP_MEMBER")
     */
    public function indexAction(Request $request)
    {
        return $this->render('@App/Admin/Industry/buyBackCalculator.html.twig');
    }
}
