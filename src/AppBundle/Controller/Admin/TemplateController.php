<?php

namespace AppBundle\Controller\Admin;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/template", options={"expose"=true})
 */
class TemplateController extends Controller
{
    /**
     * @Route("/server_status", name="template.serverstatus")
     */
    public function statusAction(Request $request)
    {
        return $this->render('AppBundle:Templates:serverStatus.html.twig');
    }

    /**
     * @Route("/data_table", name="template.datatable")
     */
    public function datatableAction(Request $request)
    {
        return $this->render('AppBundle:Templates:dataTable.html.twig');
    }

    /**
     * @Route("/reauthentication", name="template.reAuthentication")
     */
    public function reauthAction(Request $request)
    {
        return $this->render('AppBundle:Templates:reAuthentication.html.twig');
    }

    /**
     * @Route("/slide_menu", name="template.slidemenu")
     */
    public function slideAction(Request $request)
    {
        return $this->render('AppBundle:Templates:slideMenuWrapper.html.twig');
    }

    /**
     * @Route("/slide_button", name="template.slidebutton")
     */
    public function slideButtonAction(Request $request)
    {
        return $this->render('AppBundle:Templates:slideButton.html.twig');
    }

    /**
     * @Route("/evetabs", name="template.evetabs")
     */
    public function evetabAction(Request $request)
    {
        return $this->render('AppBundle:Templates:eveTabs.html.twig');
    }

    /**
     * @Route("/evepane", name="template.evepanes")
     */
    public function evepaneAction(Request $request)
    {
        return $this->render('AppBundle:Templates:evePane.html.twig');
    }

    /**
     * @Route("/corpoverview", name="template.corpoverview")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function corpOverViewAction(){
        return $this->render('AppBundle:Templates:corp/corpOverview.html.twig');
    }

    /**
     * @Route("/corpinventory", name="template.corpinventory")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function corpInventoryAction(){
        return $this->render('AppBundle:Templates:corp/corpInventory.html.twig');
    }

    /**
     * @Route("/corpdeliveries", name="template.corpdeliveries")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function corpDeliveryAction(){
        return $this->render('AppBundle:Templates:corp/corpDeliveries.html.twig');
    }

    /**
     * @Route("/corp_market_orders", name="template.marketorders")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function corpMarketOrdersAction()
    {
        return $this->render('AppBundle:Templates:corp/corpMarketOrders.html.twig');
    }

    /**
     * @Route("/corp_titles", name="template.titles")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function corpTitleAction()
    {
        return $this->render('AppBundle:Templates:corp/corpTitles.html.twig');
    }

    /**
     * @Route("/api_keys", name="template.apikeys")
     * @Secure(roles="ROLE_CEO")
     */
    public function corpApiKeyAction()
    {
        return $this->render('AppBundle:Templates:corp/corpApiKeys.html.twig');
    }

    /**
     * @Route("/corp_towers", name="template.corptowers")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function corpTowerAction()
    {
        return $this->render('AppBundle:Templates:corp/corpStructures.html.twig');
    }

    /**
     * @Route("/corp_members", name="template.corpmembers")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function corpMemberAction()
    {
        return $this->render('AppBundle:Templates:corp/corpMembers.html.twig');
    }
}
