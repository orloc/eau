<?php

namespace AppBundle\Controller\Admin;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @Method("GET")
     * @Secure(roles="ROLE_CORP_MEMBER")
     */
    public function indexAction(Request $request)
    {
        return $this->render('@App/Admin/dashboard.html.twig');
    }

    /**
     * @Route("/market_manager", name="market_manager")
     * @Method("GET")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function marketManager(Request $request)
    {
        return $this->render('@App/Admin/marketManager.html.twig');
    }
}
