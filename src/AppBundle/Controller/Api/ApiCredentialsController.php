<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\ApiCredentials;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * User controller.
 *
 * @Route("/api-credentials", options={"expose"=true})
 */
class ApiCredentialsController extends AbstractController implements ApiControllerInterface {

    /**
     * Lists all API Credentials entities.
     *
     * @Route("/", name="api.api_credentials")
     * @Method("GET")
     */
    public function indexAction()
    {
        $creds = $this->getDoctrine()->getRepository('AppBundle:ApiCredentials')
            ->findAll();

        $json = $this->get('jms_serializer')->serialize($creds, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * Finds and displays a API Credentials entity.
     *
     * @Route("/{id}", name="api.api_credentials_show")
     * @Method("GET")
     * @ParamConverter("apiCreds", class="AppBundle:ApiCredentials")
     */
    public function showAction(ApiCredentials $apiCreds)
    {
        return $this->jsonResponse($this->get('serializer')->serialize($apiCreds, 'json'), 200);
    }


    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="api.api_credentials_delete")
     * @Method("DELETE")
     * @ParamConverter("apiCreds", class="AppBundle:ApiCredentials")
     */
    public function deleteAction(Request $request, ApiCredentials $apiCreds)
    {
        $apiCreds->setDeletedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();

        $em->persist($apiCreds);
        $em->flush();

        return $this->jsonResponse(null, 204);
    }
}
