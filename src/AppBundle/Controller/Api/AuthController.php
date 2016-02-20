<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Route("/auth", options={"expose"=true})
 */
class AuthController extends AbstractController implements ApiControllerInterface
{
    /**
     * @Route("/", name="api.auth")
     * @Method("GET")
     */
    public function indexAction()
    {
        $token = $this->get('security.token_storage')->getToken();

        $json = json_encode([
            'authorized' => $token->isAuthenticated(),
            'roles' => array_map(function ($d) {
                return $d->getRole();
            }, $token->getRoles()),
        ], true);

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/re-authenticate", name="api.reAuth")
     * @Method("POST")
     * @Secure(roles="ROLE_CEO")
     */
    public function reAuthenticationAction(Request $request)
    {
        $password = $request->request->get('password', null);
        $validPassword = false;
        if ($password) {
            $user = $this->getUser();
            $encoder = $this->container->get('security.password_encoder');

            $validPassword = $encoder->isPasswordValid(
                $user,
                $password,
                $user->getSalt()
            );
        }

        return $this->jsonResponse(json_encode(['result' => $validPassword]));
    }
}
