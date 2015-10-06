<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndustrialController extends Controller
{
    /**
     * @Route("/industry", name="industry.buyback")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need


        return $this->render('@App/Admin/Industry/buyBackCalculator.html.twig');
    }
}
