<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\AbstractController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="user")
     * @Route("/")
     * @Method("GET")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin/User:index.html.twig');
    }
}
