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
class RegionController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/regions", name="api.regions", options={"expose"=true})
     * @Secure(roles="ROLE_USER")
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
