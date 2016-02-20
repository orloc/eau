<?php

namespace AppBundle\Controller\Api\Eve;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Region controller.
 */
class RegionController extends AbstractController implements ApiControllerInterface
{
    /**
     * @Route("/regions", name="api.regions", options={"expose"=true})
     * @Secure(roles="ROLE_CORP_MEMBER")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $regions = $this->get('evedata.registry')
            ->get('EveBundle:Region')->getAll();

        $json = $this->get('serializer')->serialize($regions, 'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/price_regions", name="api.price_regions", options={"expose"=true})
     * @Secure(roles="ROLE_CORP_MEMBER")
     * @Method("GET")
     */
    public function availablePriceRegions(Request $request)
    {
        $rRepo = $this->get('evedata.registry')->get('EveBundle:Region');
        $rids = $this->getDoctrine()->getManager('eve_data')
            ->getRepository('EveBundle:ItemPrice')
            ->getRegionIds();

        $regions = $rRepo->getRegionsInList($rids);

        $data = array_merge([['regionName' => 'Average-Price', 'regionID' => 0]], $regions);

        return $this->jsonResponse(json_encode($data));
    }
}
