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
     * @Route("/login_redirect", name="eve.login.redirect", options={"expose": true })
     */
    public function loginRedirect(){
        return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * @Route("/registration", name="eve.register")
     */
    public function eveRegistration(Request $request){

        $session = $this->get('session');
        if (($auth = $session->get('registration_authorized', false)) !== false) {
            return $this->redirect($this->generateUrl('fos_user_registration_register'));
        }

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
