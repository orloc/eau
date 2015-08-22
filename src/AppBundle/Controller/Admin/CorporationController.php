<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/corporation")
 */
class CorporationController extends Controller
{
    /**
     * @Route("", name="corporation")
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin/Corporation:index.html.twig');
    }
}
