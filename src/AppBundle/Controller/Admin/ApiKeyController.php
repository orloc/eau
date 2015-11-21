<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\AbstractController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/api_keys")
 */
class ApiKeyController extends AbstractController
{
    /**
     * @Route("", name="api_keys.all")
     * @Route("/")
     * @Method("GET")
     * @Secure(roles="ROLE_USER")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin/Character:index.html.twig');
    }
}
