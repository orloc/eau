<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Character;
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
     * @Secure(roles="ROLE_CEO")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $members = $this->getDoctrine()->getRepository('AppBundle:CorporationMember')
            ->findBy(['corporation' => $corp]);

        $repo = $this->getRepository('AppBundle:Character');
        foreach ($members as $m){
            $found = $repo->findOneBy(['eve_id' => $m->getCharacterId()]);

            if ($found instanceof Character){
                $m->setApiKey($found->getApiCredentials()->first());
            }


        }
        $json = $this->get('serializer')->serialize($members, 'json');

        return $this->jsonResponse($json);

    }

}
