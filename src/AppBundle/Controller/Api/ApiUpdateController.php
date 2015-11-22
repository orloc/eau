<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\ApiUpdate;
use AppBundle\Entity\Corporation;
use AppBundle\Security\AccessTypes;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * ApiCredentials Controller controller.
 */
class ApiUpdateController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{id}/api_update", name="api.corporation.apiupdate", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_CEO")
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp)
    {
        $this->denyAccessUnlessGranted(AccessTypes::VIEW, $corp, 'Unauthorized access!');

        $types = [
            ApiUpdate::CORP_ACC_BALANCES,
            ApiUpdate::CORP_ASSET_LIST,
            ApiUpdate::CORP_MARKET_ORDERS,
            ApiUpdate::CORP_WALLET_TRANSACTION,
            ApiUpdate::CORP_WALLET_JOURNAL,
            ApiUpdate::CORP_ACC_BALANCES,
            ApiUpdate::CORP_CONTRACTS,
            ApiUpdate::CORP_CONTAINER_LOG,
        ];

        $type = $request->query->get('type', false);

        if (!$type || !in_array($type, $types)){
            return $this->jsonResponse(json_encode(['error' => 'Invalid type or not found']), 400);
        }

        $update = $this->getDoctrine()->getRepository('AppBundle:ApiUpdate')
            ->getLatestUpdate($corp, $type);

        $json = $this->get('serializer')->serialize($update, 'json');

        return $this->jsonResponse($json);

    }
}
