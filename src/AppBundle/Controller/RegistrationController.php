<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;


class RegistrationController extends BaseController {

    public function registerAction(Request $request){
        $session = $this->get('session');

        if ($session->get('registration_authorized', false)) {
            $session->remove('registration_authorized');

            return parent::registerAction($request);
        }

        $session->clear();

        return $this->redirect($this->generateUrl('eve.register'));
    }
}