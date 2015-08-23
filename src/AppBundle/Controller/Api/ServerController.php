<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/server", options={"expose"=true})
 */
class ServerController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/status", name="api.server.status")
     * @Method("GET")
     */
    public function indexAction()
    {
        $pheal = $this->get('tarioch.pheal.factory')->createEveOnline();
        $pheal->scope = 'server';

        $status = $pheal->serverStatus();

        $data = [
            'online' => $status->serverOpen ? true : false,
            "players" => $status->onlinePlayers
        ];

        $json = $this->get('jms_serializer')->serialize($data, 'json');

        return $this->jsonResponse($json);

    }
}
