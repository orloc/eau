<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * ApiCredentials Controller controller.
 */
class ApiCredentialsController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{id}/api_credentials", name="api.corporation.apicredentials", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $credentials = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:ApiCredentials')
            ->findBy(['corporation' => $corp]);

        $json = $this->get('serializer')->serialize($credentials, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/corporation/{id}/api_credentials", name="api.corporation.apicredentials.post", options={"expose"=true})
     * @ParamConverter(name="corporation", class="AppBundle:Corporation")
     * @Method("POST")
     */
    public function newAction(Request $request,  Corporation $corporation){

        $content = $request->request;

        $newKey = $this->get('app.apikey.manager')
            ->buildInstanceFromRequest($content);


    }

    /**
     * @Route("/corporation/{id}/api_credentials", name="api.corporation.apicredentials.update", options={"expose"=true})
     * @ParamConverter(name="credentials", class="AppBundle:ApiCredentials")
     * @Method("PATCH")
     */
    public function updateAction(Request $request, ApiCredentials $credentials)
    {
        //@TODO clean this up please
        $em = $this->getDoctrine()->getManager();

        if ($request->query->get('delete', false) && $credentials->getIsActive()){
            $credentials->setIsActive(false);

            $em->persist($credentials);
            $em->flush();
        }

        if ($request->query->get('enable', false) && !$credentials->getIsActive()){

            $credentials->setIsActive(true);

            $em->persist($credentials);
            $em->flush();
        }

        $json = $this->get('serializer')->serialize($credentials, 'json');

        return $this->jsonResponse($json);

    }
}
