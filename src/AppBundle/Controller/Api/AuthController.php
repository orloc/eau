<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthController
 * Exposes routes for re-authentication and fetching of current user roles
 * @package AppBundle\Controller\Api
 * @Route("/auth", options={"expose"=true})
 */
class AuthController extends AbstractController implements ApiControllerInterface
{
    
    /**
     * @Route("/login")
     */
    public function getTokenAction()
    {
        // The security layer will intercept this request
        return $this->jsonResponse('', 401);
    }
    /**
     * 
     * Route: GET {base}/auth/ 
     * Returns a list of roles for the current user session
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
     * Route: POST {base}/auth/re-authenticate
     * Re-authentication screen  
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
