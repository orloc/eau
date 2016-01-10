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
        $manager = $this->get('app.corp_title.manager');

        $corp = $this->getDoctrine()->getManager()->getRepository('AppBundle:Corporation')->findAll();

        echo "<pre>";
        var_dump($manager->updateTitles($corp[0]));die;
        echo "</pre>";
        return $this->render('@App/Admin/dashboard.html.twig');
    }
}
