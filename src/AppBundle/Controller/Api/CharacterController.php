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
     * @Route("/", name="api.character_create")
     * @Secure(roles="ROLE_USER")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $content = $request->request;

        $corp = $this->get('app.corporation.manager')
            ->buildInstanceFromRequest($content);

        $validator = $this->get('validator');

        $errors = $validator->validate($corp);

        if (count($errors) > 0){
            return $this->getErrorResponse($errors);
        }

        $em = $this->getDoctrine()->getManager();
        $jms = $this->get('jms_serializer');

        try {
            $em->persist($corp);
            $em->flush();
        } catch (\Exception $e){
            $this->get('logger')->warning(sprintf('Invalid API creation attempt Key: %s Code %s User_Id: %s',
                $content->get('api_key'),
                $content->get('verification_code'),
                $this->getUser() instanceof User ? $this->getUser()->getId() : '.anon'
            ));

            return $this->jsonResponse($jms->serialize([ ['message' => $e->getMessage() ]], 'json'), 400);
        }

        $this->get('app.task.dispatcher')->addDeferred(CorporationEvents::NEW_CORPORATION, new NewCorporationEvent($corp));

        $json = $jms->serialize($corp, 'json');

        return $this->jsonResponse($json, 200, [
            'Connection' => 'close'
        ]);

    }

}
