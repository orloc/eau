<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\User;
use AppBundle\Event\CorporationEvents;
use AppBundle\Event\NewCorporationEvent;
use AppBundle\Exception\InvalidExpirationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
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
     * @Route("/", name="api.corps")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        $corp = $this->getDoctrine()->getRepository('AppBundle:Corporation')
            ->findAllUpdatedCorporations();

        $json = $this->get('jms_serializer')->serialize($corp, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * @Route("/needsUpdate", name="api.corp.needs_update")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function needsUpdateAction()
    {
        $corp = $this->getDoctrine()->getRepository('AppBundle:Corporation')
            ->findToBeUpdatedCorporations();

        $json = $this->get('jms_serializer')->serialize($corp, 'json');

        return $this->jsonResponse($json);

    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="api.corp_create")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $content = $request->request;

        $keyManager = $this->get('app.apikey.manager');

        $key = $keyManager->buildInstanceFromRequest($content);

        $validator = $this->get('validator');

        $errors = $validator->validate($key);

        if (count($errors) > 0){
            return $this->getErrorResponse($errors);
        }

        $em = $this->getDoctrine()->getManager();
        $jms = $this->get('jms_serializer');
        $corpManager = $this->get('app.corporation.manager');

        try {
            $result = $this->get('app.apikey.manager')
                ->validateAndUpdateApiKey($key);

            $result_key = $result->toArray()['result']['key'];

            $keyManager->updateKey($key, $result_key);

            $character = array_pop($result_key['characters']);

            $key->setEveCharacterId($character['characterID'])
                ->setEveCorporationId($character['corporationID']);

            $corp = $corpManager->createNewCorporation($key);

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

        #$this->get('app.task.dispatcher')->addDeferred(CorporationEvents::NEW_CORPORATION, new NewCorporationEvent($corp));

        $json = $jms->serialize($corp, 'json');

        return $this->jsonResponse($json, 200, [
            'Connection' => 'close'
        ]);

    }
}
