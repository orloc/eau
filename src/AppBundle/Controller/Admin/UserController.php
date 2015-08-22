<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("", name="user")
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin/User:index.html.twig');
    }
}
