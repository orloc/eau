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
     * @Route("/registration", name="eve.register")
     */
    public function eveRegistration(Request $request){

        return $this->render('@App/Marketing/eve_registration.html.twig');
    }

    /**
     * @Route("/legal", name="legal")
     */
    public function legalAction(Request $request){
        return $this->isGranted('ROLE_AUTHENTICATED_FULLY')
            ? $this->render('@App/Admin/legal.html.twig')
            : $this->render('@App/Marketing/legal.html.twig');
    }
}
