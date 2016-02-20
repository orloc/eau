<?php

namespace AppBundle\Controller\Api\Eve;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Price controller.
 */
class PriceController extends AbstractController implements ApiControllerInterface
{
    /**
     * @Route("/prices/{id}", name="api.price.average", options={"expose"=true})
     * @Secure(roles="ROLE_CORP_MEMBER")
     * @Method("GET")
     */
    public function indexAction(Request $request, $id)
    {
        $em = $this->get('doctrine')->getManager('eve_data');

        $price = $em->getRepository('EveBundle:AveragePrice')
            ->getAveragePriceByType($id);

        $json = $this->get('serializer')->serialize($price, 'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/prices", name="api.price.averagelist", options={"expose"=true})
     * @Secure(roles="ROLE_CORP_MEMBER")
     * @Method("GET")
     */
    public function getListAction(Request $request)
    {
        $ids = $request->query->get('typeId', false);
        $repo = $this->getDoctrine()->getManager('eve_data')
            ->getRepository('EveBundle:AveragePrice');

        if (!$ids) {
            $entities = $repo->findAll();
        } else {
            $intIds = array_map(function ($id) {
                return intval($id);
            }, array_unique($ids));

            $entities = $repo->findInList($intIds);
        }

        $json = $this->get('serializer')->serialize($entities, 'json');

        return $this->jsonResponse($json);
    }
}
