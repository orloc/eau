<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
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
        } catch (InvalidExpirationException $e){
            $this->get('logger')->warning('Invalid API creation attempt Key: %s Code %s User_Id: %s',
                $content->get('api_key'),
                $content->get('verification_code'),
                $this->getUser()->getId()
            );

            return $this->jsonResponse($jms->serialize([ ['message' => $e->getMessage() ]], 'json'), 400);
        }

        die;
        $em->flush();

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
