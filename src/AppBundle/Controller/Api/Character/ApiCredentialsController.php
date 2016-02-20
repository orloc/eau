<?php

namespace AppBundle\Controller\Api\Character;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Character;
use AppBundle\Entity\CorporationMember;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * ApiCredentials Controller controller.
 */
class ApiCredentialsController extends AbstractController implements ApiControllerInterface
{
    /**
     * @Route("/{id}/api_credentials", name="api.character.apicredentials", options={"expose"=true})
     * @ParamConverter(name="character", class="AppBundle:CorporationMember")
     * @Method("GET")
     * @Secure(roles="ROLE_CORP_MEMBER")
     */
    public function getCharacterKeys(Request $request, CorporationMember $character)
    {

        //$this->denyAccessUnlessGranted(AccessTypes::VIEW, $character, 'Unauthorized access!');

        $repo = $this->getDoctrine()->getRepository('AppBundle:ApiCredentials');

        $keys = $repo->findRelatedKeyByMember($character);

        return $this->jsonResponse($this->get('serializer')->serialize($keys, 'json'), 200);
    }

    /**
     * @Route("/{id}/api_credentials", name="api.character.apicredentials.update", options={"expose"=true})
     * @ParamConverter(name="character", class="AppBundle:Character")
     * @Method("POST")
     * @Secure(roles="ROLE_CORP_MEMBER")
     */
    public function addCharacterKey(Request $request, Character $character)
    {
        $this->denyAccessUnlessGranted(AccessTypes::EDIT, $character, 'Unauthorized access!');

        $user = $this->getUser();

        $content = $request->request;

        $newKey = $this->get('app.apikey.manager')
            ->buildInstanceFromRequest($content);

        $validator = $this->get('validator');

        $errors = $validator->validate($newKey);

        if (count($errors) > 0) {
            return $this->getErrorResponse($errors);
        }

        try {
            $result = $this->get('app.apikey.manager')
                ->validateAndUpdateApiKey($newKey);

            $eveDetails = $result->key->characters[0];

            if ($character->getEveId() !== $eveDetails->characterID) {
                return $this->jsonResponse(json_encode(['message' => 'character / api key mismatch', 'property_path' => '']), 409);
            }
        } catch (\Exception $e) {
            $this->get('logger')->addEmergency('Error registering new api key for user '.$user->getId());

            return $this->jsonResponse(json_encode(['message' => 'Error with the EVE Api please try again', 'property_path' => '']), 400);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($character);

        $em->flush();

        $json = $this->get('serializer')->serialize($newKey, 'json');

        return $this->jsonResponse($json);
    }
}
