<?php

namespace AppBundle\Controller;

use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Util\StringUtils;

class EveSSOController extends Controller
{
    /**
     * @Route("/redirect_sso", name="redirect_sso")
     */
    public function redirectAction(Request $request)
    {
        $ssoUrl = "https://login.eveonline.com/oauth/authorize/";

        $gen = new SecureRandom();
        $nonce =  md5($gen->nextBytes(10));

        $session = $this->get('session');

        $session->set('eve_sso_nonce', $nonce);

        $params = [
            'response_type' => 'code',
            'redirect_uri' => $this->generateUrl('sso_callback', [], true),
            'scope' => "",
            'client_id' => $this->container->getParameter('eve_client_id'),
            'state' => $nonce
        ];

        $pieces = [];
        foreach ($params as $k => $v){
            $pieces[] = "$k=$v";
        }

        $fullUrl = $ssoUrl.'?'.implode("&",$pieces);

        return $this->redirect($fullUrl);

    }

    /**
     * @Route("/sso_callback", name="sso_callback")
     */
    public function callbackAction(Request $request){

        $state = $request->query->get('state', null);
        $code = $request->query->get('code', null);

        $session = $this->get('session');
        $nonce = $session->get('eve_sso_nonce');

        if (!StringUtils::equals($nonce, $state)){
            $session->getFlashBag()->add('danger', 'Authentication Nonce does not match - your request may have been intercepted by a malicious 4th party.');
            return $this->redirect($this->generateUrl('default'));
        }

        $auth_uri = "https://login.eveonline.com/oauth/token";

        $client = new Client();

        $creds = [
            trim($this->container->getParameter('eve_client_id')),
            trim($this->container->getParameter('eve_client_secret'))
        ];

        /*
         * LOOK OUT FOR THE SPACE
         */
        $request = new \GuzzleHttp\Psr7\Request('POST', $auth_uri, [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic '.base64_encode(implode(":", $creds))
        ], "grant_type=authorization_code&code=$code");

        try {
            $response = $client->send($request, [ 'timeout' => 2]);
        } catch (\Exception $e){
            $session->getFlashBag()->add('danger', 'There was <b>EITHER</b> a serious error when attempting to authenticate you <b>OR</b> the request you had sent was invald! <br><b><i>Try Again - if this persists - Don\'t worry we are fixing it...</i></b>');

            return $this->redirect($this->generateUrl('fos_user_registration_register'));
        }

        $response_content = json_decode($response->getBody()->getContents());

        var_dump($response_content);die;

    }
}
