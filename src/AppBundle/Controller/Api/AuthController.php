<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/auth", options={"expose"=true})
 */
class AuthController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/", name="api.auth")
     * @Method("GET")
     */
    public function indexAction()
    {
        $token = $this->get('security.token_storage')->getToken();

        $json = json_encode([
            'authorized' => $token->isAuthenticated(),
            'roles' => array_map(function($d) {
                return $d->getRole();
            }, $token->getRoles())
        ], true);

        return $this->jsonResponse($json);

    }

}
