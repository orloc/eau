<?php

namespace AppBundle\Controller\Admin;

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
     * @Route("/loading_spinner", name="template.loading.spinner")
     */
    public function loadingSpinnerAction(Request $request)
    {
        return $this->render('AppBundle:Templates:loadingSpinner.html.twig');
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
}
