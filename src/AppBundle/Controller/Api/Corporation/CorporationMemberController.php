<?php

namespace AppBundle\Controller\Api\Corporation;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Character;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Region controller.
 */
class CorporationMemberController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/{id}/members", name="api.corporation.members", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     * @Secure(roles="ROLE_DIRECTOR")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');
        $members = $this->getDoctrine()->getRepository('AppBundle:CorporationMember')
            ->findBy(['corporation' => $corp]);

        $tmp = [];
        foreach ($members as $m){
            $tmp[$m->getCharacterName()] = $m;
        }

        $repo = $this->getRepository('AppBundle:Character');
        foreach ($members as $m){
            $found = $repo->findOneBy(['eve_id' => $m->getCharacterId(), 'is_main' => true]);

            if ($found instanceof Character){
                $tmp[$m->getCharacterName()]->setApiKey($found->getApiCredentials()->first() instanceof ApiCredentials);
                $vals = array_values($found->associatedCharacters());
                $tmp[$m->getCharacterName()]->setAssociatedChars($vals);

                foreach ($vals as $v){
                    if (isset($tmp[$v['name']])){
                        unset($tmp[$v['name']]);
                    }
                }

            }
        }

        $json = $this->get('serializer')->serialize($tmp, 'json');

        return $this->jsonResponse($json);

    }

}
