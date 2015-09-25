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
        // replace this example code with whatever you need

        /*
        $ore_groups = $this->get('evedata.registry')->get('EveBundle:MarketGroup')
            ->getOreGroups();

        $item_repo = $this->get('evedata.registry')->get('EveBundle:ItemType') ;

        $oreTypes = [];
        foreach($ore_groups as $ore){

            $res = $item_repo->findTypesByGroupId($ore['marketGroupID']);

            $oreTypes[$ore['marketGroupName']] = $res;

        }

        var_dump($oreTypes);die;
        */

        return $this->render('@App/Admin/dashboard.html.twig');
    }
}
