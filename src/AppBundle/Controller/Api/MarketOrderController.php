<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * MarketOrder controller.
 *
 */
class MarketOrderController extends AbstractController implements ApiControllerInterface
{

    /**
     * @Route("/corporation/{id}/marketorders", name="api.corporation.marketorders", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     */
    public function indexAction(Corporation $corp)
    {

        $json = $this->get('jms_serializer')->serialize($corp, 'json');

        return $this->jsonResponse($json);

    }
}
