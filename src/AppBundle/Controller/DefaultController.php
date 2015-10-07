<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('@App/Marketing/landing.html.twig');
    }

    /**
     * @Route("/legal", name="legal")
     */
    public function legalAction(Request $request){
        return $this->render('@App/legal.html.twig');

    }
}
