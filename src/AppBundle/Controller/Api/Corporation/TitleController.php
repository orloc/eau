<?php

namespace AppBundle\Controller\Api\Corporation;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Corporation controller.
 */
class TitleController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/{$id}/titles", name="api.titles", options={"expose"=true})
     * @ParamConverter(class="AppBundle\Entity\Corporation", name="corp")
     * @Secure(roles="ROLE_DIRECTOR")
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp)
    {
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $titles = $this->getDoctrine()->getRepository('AppBundle:CorporationTitle')
            ->findBy(['corporation' => $corp]);

        $json = $this->get('serializer')->serialize($titles, 'json');

        return $this->jsonResponse($json);

    }
}
