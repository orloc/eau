<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Character controller.
 */
class CharacterController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/characters", name="api.characters", options={"expose"=true})
     * @Secure(roles="ROLE_USER")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        $user = $this->getUser();

        $characters = $this->getDoctrine()->getRepository('AppBundle:Character')
            ->findBy(['user' => $user ]);

        $json = $this->get('serializer')->serialize($characters, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * Creates a new corporation entity.
     *
     * @Route("/characters", name="api.character_create", options={"expose"=true})
     * @Secure(roles="ROLE_USER")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $content = $request->request;


        $char = $this->get('app.character.manager')
            ->buildInstanceFromRequest($content);

        $validator = $this->get('validator');

        $errors = $validator->validate($char);

        if (count($errors) > 0){
            return $this->getErrorResponse($errors);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $jms = $this->get('jms_serializer');

        $user->addCharacter($char);

        try {
            $result = $this->get('app.apikey.manager')
                ->validateAndUpdateApiKey($char->getApiCredentials()[0]);

            $eveDetails = $result->key->characters[0];
            $char->setName($eveDetails->characterName)
                ->setEveId($eveDetails->characterID);
        } catch (\Exception $e){

        }


        try {
            $em->persist($char);
            $em->flush();
        } catch (\Exception $e) {
            return $this->jsonResponse(json_encode(['message' => $e->getMessage(), 'code' => 409]), 409);
        }

        $json = $jms->serialize($char, 'json');

        return $this->jsonResponse($json, 200);
    }

}
