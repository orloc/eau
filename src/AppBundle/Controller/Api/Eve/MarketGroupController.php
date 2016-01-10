<?php

namespace AppBundle\Controller\Api\Eve;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * MarketOrder controller.
 *
 */
class MarketGroupController extends AbstractController implements ApiControllerInterface
{

    /**
     * @Route("/market_groups", name="api.marketgroups", options={"expose"=true})
     * @Method("GET")
     * @TODO NOT USED
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction(Request $requst)
    {

        $registry = $this->get('evedata.registry');
        $groups = $registry->get('EveBundle:MarketGroup')
            ->getTopLevelGroups();

        $json = json_encode($groups);

        return $this->jsonResponse($json);
    }
}
