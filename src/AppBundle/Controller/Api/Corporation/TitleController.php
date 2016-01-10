<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Region controller.
 */
class TitleController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{$id}/titles", name="api.titles", options={"expose"=true})
     * @Secure(roles="ROLE_DIRECTOR")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        $titles = $this->get('evedata.registry')
            ->get('EveBundle:Region')->getAll();

        $json = $this->get('serializer')->serialize($regions, 'json');

        return $this->jsonResponse($json);

    }
}
