<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function indexAction(Request $request)
    {

        /*
        $registry = $this->get('evedata.registry');
        $groups = $registry->get('EveBundle:MarketGroup')
            ->getAllGroups();

        $groupIds = array_map(function($d){ return $d['marketGroupID']; }, $groups);
        $marketItems = $registry->get('EveBundle:ItemType')->findAllInGroups($groupIds);

        var_dump($marketItems);die;
        */
        return $this->render('@App/Admin/dashboard.html.twig');
    }
}
