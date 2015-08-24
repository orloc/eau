<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\User;
use AppBundle\Event\CorporationEvents;
use AppBundle\Event\NewCorporationEvent;
use AppBundle\Exception\InvalidExpirationException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/corporation", options={"expose"=true})
 */
class CorporationController extends AbstractController implements ApiControllerInterface {

    /**
     * Lists all User entities.
     *
     * @Route("/", name="api.corps")
     * @Method("GET")
     */
    public function indexAction()
    {
        $corp = $this->getDoctrine()->getRepository('AppBundle:Corporation')
            ->findAll();

        $json = $this->get('jms_serializer')->serialize($corp, 'json');

        return $this->jsonResponse($json);

    }
    /**
     * Creates a new User entity.
     *
     * @Route("/", name="api.corp_create")
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

        return $this->jsonResponse($json);

    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="api.corp_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="api.corp_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
    }
}
