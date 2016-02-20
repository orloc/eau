<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MarketStatisticController extends AbstractController implements ApiControllerInterface
{
    /**
     * @Route("/industry/market_diff", name="api.market_diff", options={"expose"=true})
     * @Method("GET")
     * @Secure(roles="ROLE_USER")
     */
    public function getPriceDistributionAction(Request $request)
    {
        $authChecker = $this->get('security.authorization_checker');
        if ($authChecker->isGranted('ROLE_DIRECTOR')) {
            $em = $this->getDoctrine()->getManager();
            $main = $em->getRepository('AppBundle:Character')->getMainCharacter($this->getUser());
            if ($main !== null) {
                $corporation = $this->getDoctrine()->getRepository('AppBundle:Corporation')
                    ->findByCorpName($main->getCorporationName());

                $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corporation, 'Unauthorized access!');
            }
        }

        if (isset($corporation) && $corporation instanceof Corporation) {
        }

        return $this->jsonResponse($this->get('jms_serializer')->serialize([], 'json'));
    }
}
