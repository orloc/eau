<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * User controller.
 *
 * @Route("/user", options={"expose"=true})
 */
class UserController extends AbstractController implements ApiControllerInterface {

    /**
     * Lists all User entities.
     *
     * @Route("/", name="api.users")
     * @Method("GET")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')
            ->getUsers();

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
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $errors = $this->processRequest($user, $request->request);

        if (count($errors) > 0 ){
            return $this->getErrorResponse($errors);
        }

        $jms = $this->get('serializer');

        try {
            $userManager->updateUser($user, true);
        } catch (\Exception $e){
            return $this->jsonResponse($jms->serialize([ ['message' => 'There was an error with this request - likely the email OR username is already taken.']], 'json'), 409);
        }

        return $this->jsonResponse($jms->serialize($user, 'json'), 201, [
            'Location' => $this->generateUrl('api.user_show', [ 'id' => $user->getId()])
        ]);
    }

    private function processRequest(User $user, ParameterBag $content){
        $user->setUsername($content->get('username'))
            ->setEmail($content->get('email'))
            ->setPlainPassword($content->get('password'))
            ->addRole($content->get('role'));

        $validator = $this->get('validator');

        return $validator->validate($user);
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="api.user_show")
     * @Method("GET")
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function showAction($id, User $user)
    {
        return $this->jsonResponse($this->get('serializer')->serialize($user, 'json'), 200);
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="api.user_update")
     * @ParamConverter("user", class="AppBundle:User")
     * @Method("PUT")
     */
    public function updateAction(Request $request, User $user)
    {
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="api.user_delete")
     * @Method("DELETE")
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function deleteAction(Request $request, User $user)
    {
        $user->setDeletedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        return $this->jsonResponse(null, 204);
    }
}
