<?php

namespace AppBundle\Controller\Api\Corporation;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * ApiCredentials Controller controller.
 */
class ApiCredentialsController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/{id}/api_credentials", name="api.corporation.apicredentials", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     * @Secure(roles="ROLE_CEO")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $credentials = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:ApiCredentials')
            ->findBy(['corporation' => $corp]);

        $json = $this->get('serializer')->serialize($credentials, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/{id}/api_credentials", name="api.corporation.apicredentials.post", options={"expose"=true})
     * @ParamConverter(name="corporation", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("POST")
     */
    public function newAction(Request $request,  Corporation $corporation){

        $this->denyAccessUnlessGranted(AccessTypes::EDIT, $corporation, 'Unauthorized access!');

        $content = $request->request;

        $newKey = $this->get('app.apikey.manager')
            ->buildInstanceFromRequest($content);

        $validator = $this->get('validator');

        $errors = $validator->validate($newKey);

        if (count($errors) > 0){
            return $this->getErrorResponse($errors);
        }

        try {
            $result = $this->get('app.apikey.manager')
                ->validateAndUpdateApiKey($newKey);

            $eveDetails = $result->key->characters[0];

            $newKey->setEveCharacterId($eveDetails['characterID'])
                ->setEveCorporationId($eveDetails['corporationID']);

            $corporation->addApiCredential($newKey);

            $em = $this->getDoctrine()->getManager();

            $em->persist($newKey);

            $em->flush();
        } catch (\Exception $e){
            $this->get('logger')->addEmergency('Error registering new api key for user '.$corporation->getCorporationDetails()->getName());
            return $this->jsonResponse(json_encode(['message' => 'Error with the EVE Api please try again', 'property_path' => '', 'exception' => $e->getMessage()]), 400);
        }

        return $this->jsonResponse($this->get('serializer')->serialize($newKey, 'json'));


    }

    /**
     * @Route("/{id}/api_credentials", name="api.corporation.apicredentials.update", options={"expose"=true})
     * @ParamConverter(name="credentials", class="AppBundle:ApiCredentials")
     * @Secure(roles="ROLE_CEO")
     * @Method("PATCH")
     */
    public function updateAction(Request $request, ApiCredentials $credentials)
    {
        $this->denyAccessUnlessGranted(AccessTypes::EDIT, $credentials->getCorporation(), 'Unauthorized access!');

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
