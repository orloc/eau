<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Character;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Util\StringUtils;

class EveSSOController extends Controller
{
    const BASE_URI = 'https://login.eveonline.com/oauth/',
        AUTH_URI = self::BASE_URI.'authorize',
        TOKEN_URI = self::BASE_URI.'token',
        VERIFY_URI = self::BASE_URI.'verify';
    /**
     * @Route("/redirect_sso", name="redirect_sso")
     */
    public function redirectAction()
    {
        $gen = new SecureRandom();
        $nonce = md5($gen->nextBytes(10));
        $session = $this->get('session');
        $session->set('eve_sso_nonce', $nonce);

        return $this->redirect(join('',[self::AUTH_URI,'?',http_build_query([
            'response_type' => 'code',
            'redirect_uri' => $this->generateUrl('sso_callback', [], true),
            'scope' => '',
            'client_id' => $this->container->getParameter('eve_client_id'),
            'state' => $nonce,
        ])]));
    }

    /**
     * @Route("/sso_callback", name="sso_callback")
     */
    public function callbackAction(Request $request)
    {
        $state = $request->query->get('state', null);
        $code = $request->query->get('code', null);

        $session = $this->get('session');
        $nonce = $session->get('eve_sso_nonce');
        $session->remove('eve_sso_nonce');

        if (!StringUtils::equals($nonce, $state)) {
            return $this->redirect($this->generateUrl('eve.register'));
        }

        $auth_request = $this->buildAuthRequest($code);
        
        try {
            $response = $this->tryRequest($auth_request);
            return  $this->verifySSOResponse($response);
        } catch (\Exception $e) {
            $session->getFlashBag()->add('danger', 'There was a problem with your request<i>Try Again - if this persists - Submit an issue ticket using the link in the footer.</i></b>');
            return $this->redirect($this->generateUrl('eve.register'));
        }

    }
    
    protected function verifySSOResponse(Response $response){
        $response_content = json_decode($response->getBody()->getContents());
        $token = $response_content->access_token;

        $verfiyRequest = new \GuzzleHttp\Psr7\Request('GET', self::VERIFY_URI, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $charResponse = $this->tryRequest($verfiyRequest);

        return $this->applyRegistrationRules(json_decode($charResponse->getBody()->getContents()));
    }
    
    protected function applyRegistrationRules($decoded){
        $session = $this->get('session');
        $cId = $decoded->CharacterID;
        $cName = $decoded->CharacterName;
        
        $isRegistered = $this->getDoctrine()->getRepository('AppBundle:Character')
            ->findOneBy(['eve_id' => $cId]);
        
        if ($isRegistered instanceof Character) {
            $session->getFlashBag()->add('warning', 'This character is already associated with a user.');
            return $this->redirect($this->generateUrl('eve.register'));
        }

        $canRegister = $this->getDoctrine()->getRepository('AppBundle:CorporationMember')->findOneBy(['character_id' => intval($cId)]);

        // character isnt in a corp that is registered by an admin
        if (!$canRegister) {
            $session->getFlashBag()->add('warning', 'Sorry we do not support non-alpha tester registrations at this time.<br><b>COME BACK SOON</b> or make a request to add your corporation through a support ticket below.');
            $this->get('logger')->info(sprintf('ATTEMPTED REGISTRATION: char_id = %s char_name = %s', $cId, $cName));
            return $this->redirect($this->generateUrl('eve.register'));
        } 
        
        $session->set('registration_authorized', ['id' => $cId, 'name' => $cName]);
        
        return $this->redirect($this->generateUrl('fos_user_registration_register'));
    }
    
    protected function buildAuthRequest($code) {
        $credentials = [
            trim($this->container->getParameter('eve_client_id')),
            trim($this->container->getParameter('eve_client_secret')),
        ];
        
        return  new \GuzzleHttp\Psr7\Request('POST', self::TOKEN_URI, [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic '.base64_encode(implode(':', $credentials)),
        ], "grant_type=authorization_code&code=$code");
    }

    protected function tryRequest(\GuzzleHttp\Psr7\Request $request)
    {
        $client = new Client();
        $response = $client->send($request, ['timeout' => 2]);
        return $response;
    }
}
