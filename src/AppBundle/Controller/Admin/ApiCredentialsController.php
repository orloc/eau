<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/api-keys")
 */
class ApiCredentialsController extends Controller
{
    /**
     * @Route("", name="api_credentials")
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin/ApiCredentials:index.html.twig');
    }
}
