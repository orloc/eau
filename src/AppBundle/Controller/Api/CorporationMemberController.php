<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
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
     * @Route("/corporation/{id}/members", name="api.corporation.members", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $members = $this->getDoctrine()->getRepository('AppBundle:CorporationMember')
            ->findBy(['corporation' => $corp]);

        $repo = $this->getRepository('AppBundle:ApiCredentials');
        foreach ($members as $m){
            $found = $repo->findOneBy(['eve_character_id' => $m->getCharacterId(), 'type' => 'Character']);

            if ($found instanceof ApiCredentials){
                $m->setApiKey($found);
            }
        }

        $json = $this->get('serializer')->serialize($members, 'json');

        return $this->jsonResponse($json);

    }

}
