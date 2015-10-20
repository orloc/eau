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
 * @Route("/character")
 */
class CharacterController extends AbstractController
{
    /**
     * @Route("", name="characters")
     * @Route("/")
     * @Method("GET")
     * @Secure(roles="ROLE_USER")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin/Character:index.html.twig');
    }
}
