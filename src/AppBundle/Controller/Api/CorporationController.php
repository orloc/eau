<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
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

        $em->persist($corp);
        $em->flush();

        $json = $this->get('jms_serializer')->serialize($corp, 'json');

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
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="api.corp_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
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
