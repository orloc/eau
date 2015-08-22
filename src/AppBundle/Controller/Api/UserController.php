<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends AbstractController {

    /**
     * Lists all User entities.
     *
     * @Route("/", name="api.users")
     * @Method("GET")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findAll();

        $json = $this->get('jms_serializer')->serialize($users, 'json');

        return $this->jsonResponse($json);

    }
    /**
     * Creates a new User entity.
     *
     * @Route("/", name="api.user_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="api.user_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="api.user_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="api.user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
    }
}
