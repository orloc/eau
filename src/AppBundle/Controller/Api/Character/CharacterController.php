<?php

namespace AppBundle\Controller\Api\Character;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Character controller.
 */
class CharacterController extends AbstractController implements ApiControllerInterface
{
    /**
     * @Route("/", name="api.characters", options={"expose"=true})
     * @Secure(roles="ROLE_CORP_MEMBER")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        $characters = $this->getDoctrine()->getRepository('AppBundle:Character')
            ->findBy(['user' => $user]);

        $json = $this->get('serializer')->serialize($characters, 'json');

        return $this->jsonResponse($json);
    }

    /**
     * Creates a new character entity.
     *
     * @Route("/", name="api.character_create.validate", options={"expose"=true})
     * @Secure(roles="ROLE_CORP_MEMBER")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $content = $request->request;

        $key = $this->get('app.apikey.manager')
            ->buildInstanceFromRequest($content);

        $validator = $this->get('validator');

        $errors = $validator->validate($key);

        if (count($errors) > 0) {
            return $this->getErrorResponse($errors);
        }

        try {
            $result = $this->get('app.apikey.manager')
                ->validateAndUpdateApiKey($key, 'Account', '1073741823');

            $arr = $result->toArray();

            $arr['result']['key']['api_key'] = $key->getApiKey();
            $arr['result']['key']['verification_code'] = $key->getVerificationCode();
            $arr['result']['key']['access_mask'] = $key->getAccessMask();
            $arr['result']['key']['type'] = $key->getType();

            $corps = $this->getDoctrine()->getRepository('AppBundle:Corporation');

            foreach ($arr['characters'] as $i => $c) {
                $exists = $corps->findOneBy(['eve_id' => $c['corporationID']]);

                $arr['characters'][$i]['best_guess'] = $exists instanceof Corporation;
            }

            return $this->jsonResponse(json_encode($arr));
        } catch (\Exception $e) {
            return $this->jsonResponse(json_encode(['message' => $e->getMessage(), 'code' => 400]), 400);
        }
    }

    /**
     * Creates a new character entity step 2.
     *
     * @Route("/final", name="api.character_create.finalize", options={"expose"=true})
     * @Secure(roles="ROLE_CORP_MEMBER")
     * @Method("POST")
     */
    public function createFinalAction(Request $request)
    {
        $content = $request->request;

        $initialKey = $content->get('full_key', null);
        $selected_char = $content->get('char', null);
        $api_key = $initialKey['result']['key'];

        $keyManager = $this->get('app.apikey.manager');
        $key = $keyManager->buildInstanceFromArray($api_key);

        $validator = $this->get('validator');

        $errors = $validator->validate($key);

        if (count($errors) > 0) {
            return $this->getErrorResponse($errors);
        }

        $key->setEveCharacterId($selected_char['characterID'])
            ->setEveCorporationId($selected_char['corporationID']);

        $all_chars = $initialKey['characters'];
        $cmanager = $this->get('app.character.manager');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $addedChars = [];

        $has_main = $this->getDoctrine()->getRepository('AppBundle:Character')
            ->findOneBy(['user' => $user, 'is_main' => true]);

        foreach ($all_chars as $c) {
            $char = $cmanager->createCharacter($c);

            if ($char->getEveId() === $selected_char['characterID'] && !$has_main) {
                $char->setIsMain(true);
            }

            $char->addApiCredential($key);
            $char->setUser($user);

            $em->persist($char);

            array_push($addedChars, $char);
        }

        try {
            $em->flush();

            return  $this->jsonResponse($this->get('jms_serializer')->serialize($addedChars, 'json'));
        } catch (\Exception $e) {
            return $this->jsonResponse(json_encode(['message' => $e->getMessage(), 'code' => 400]), 400);
        }
    }
}
