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
        $session->remove('eve_sso_nonce');

        if (!StringUtils::equals($nonce, $state)){
            $session->getFlashBag()->add('danger', 'Authentication Nonce does not match - your request may have been intercepted by a malicious 4th party.');
            return $this->redirect($this->generateUrl('default'));
        }

        $auth_uri = "https://login.eveonline.com/oauth/token";

        $creds = [
            trim($this->container->getParameter('eve_client_id')),
            trim($this->container->getParameter('eve_client_secret'))
        ];

        /*
         * LOOK OUT FOR THE SPACE
         */
        $auth_request = new \GuzzleHttp\Psr7\Request('POST', $auth_uri, [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic '.base64_encode(implode(":", $creds))
        ], "grant_type=authorization_code&code=$code");

        try {
            $response = $this->tryRequest($auth_request);
        } catch (\Exception $e){
            $session->getFlashBag()->add('danger', 'There was <b>EITHER</b> a serious error when attempting to authenticate you <b>OR</b> the request you had sent was invald! <br><b><i>Try Again - if this persists - Don\'t worry we are fixing it...</i></b>');

            return $this->redirect($this->generateUrl('eve.register'));
        }

        $response_content = json_decode($response->getBody()->getContents());
        $token = $response_content->access_token;

        $verify_uri = "https://login.eveonline.com/oauth/verify";

        $verfiyRequest = new \GuzzleHttp\Psr7\Request('GET', $verify_uri, [
            'Authorization' => 'Bearer '.$token
        ]);

        try {
            $charResponse = $this->tryRequest($verfiyRequest);
        } catch (\Exception $e){
            $session->getFlashBag()->add('danger', 'There was <b>EITHER</b> a serious error when attempting to authenticate you <b>OR</b> the request you had sent was invald! <br><b><i>Try Again - if this persists - Don\'t worry we are fixing it...</i></b>');

            return $this->redirect($this->generateUrl('eve.register'));
        }

        $decoded = json_decode($charResponse->getBody()->getContents());

        $cId = $decoded->CharacterID;
        $cName = $decoded->CharacterName;

        $exists = $this->getDoctrine()->getRepository('AppBundle:CorporationMember')->findOneBy(['character_id' => intval($cId)]);

        // character isnt in a corp that is registered by an admin
        if ($exists === null){
            $session->getFlashBag()->add('warning', 'Sorry we do not support non-alpha tester registrations at this time.<br><b>COME BACK SOON</b>');

            $this->get('logger')->info(sprintf("ATTEMPTED REGISTRATION: char_id = %s char_name = %s", $cId, $cName ));
            return $this->redirect($this->generateUrl('eve.register'));

        } else {
            // all is well
            $session->set('registration_authorized', true);
            $session->set('registration_charname', $cName);
            return $this->redirect($this->generateUrl('fos_user_registration_register'));
        }

    }

    protected function tryRequest(\GuzzleHttp\Psr7\Request $request){
        $client = new Client();

        $response = $client->send($request, [ 'timeout' => 2]);

        return $response;

    }
}
