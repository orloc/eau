<?php

namespace AppBundle\Controller\Api\Corporation;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Corporation controller.
 */
class TitleController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/{$id}/titles", name="api.titles", options={"expose"=true})
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
