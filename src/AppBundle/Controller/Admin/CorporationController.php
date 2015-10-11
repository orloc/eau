<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\AbstractController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/corporation")
 */
class CorporationController extends AbstractController
{
    /**
     * @Route("", name="corporation")
     * @Route("/")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin/Corporation:index.html.twig');
    }
}
