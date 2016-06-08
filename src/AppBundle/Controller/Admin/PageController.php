<?php

namespace AppBundle\Controller\Admin;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{
    
    protected function generateHackToken(){
        $user = $this->getUser();
        $manager = $this->get('lexik_jwt_authentication.jwt_manager');
        
        return $manager->create($user);
    }
    /**
     * @Route("/dashboard", name="dashboard")
     * @Method("GET")
     * @Secure(roles="ROLE_CORP_MEMBER")
     */
    public function indexAction(Request $request)
    {
        return $this->render('@App/Admin/dashboard.html.twig', [
            'token' => $this->generateHackToken()   
        ]);
    }


    /**
     * @Route("/industry", name="industry.buyback")
     * @Method("GET")
     * @Secure(roles="ROLE_CORP_MEMBER")
     */
    public function industryAction(Request $request)
    {
        return $this->render('@App/Admin/Industry/buyBackCalculator.html.twig');
    }

    /**
     * @Route("/industry/price-helper", name="industry.price_helper")
     * @Method("GET")
     * @Secure(roles="ROLE_CORP_MEMBER")
     */
    public function industryHelperAction(Request $request)
    {
        return $this->render('@App/Admin/Industry/priceHelper.html.twig');
    }

    /**
     * @Route("", name="characters")
     * @Route("/character/")
     * @Method("GET")
     * @Secure(roles="ROLE_CORP_MEMBER")
     */
    public function characterAction()
    {
        return $this->render('AppBundle:Admin/Character:index.html.twig');
    }

    /**
     * @Route("", name="corporation")
     * @Route("/corporation/")
     * @Method("GET")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function corporationAction()
    {
        return $this->render('AppBundle:Admin/Corporation:index.html.twig');
    }

    /**
     * @Route("/market_manager", name="market_manager")
     * @Method("GET")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function marketManager(Request $request)
    {
        return $this->render('@App/Admin/marketManager.html.twig');
    }

    /**
     * @Route("", name="user")
     * @Route("/user/")
     * @Method("GET")
     * @Secure(roles="ROLE_CEO")
     */
    public function userAction()
    {
        return $this->render('AppBundle:Admin/User:index.html.twig');
    }
}
