<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Region controller.
 */
class RegionController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/regions", name="api.regions", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        $regions = $this->get('evedata.registry')
            ->get('EveBundle:Region')->getAll();

        $json = $this->get('serializer')->serialize($regions, 'json');

        return $this->jsonResponse($json);

    }

}
