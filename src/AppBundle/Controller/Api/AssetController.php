<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Account controller.
 */
class AssetController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{id}/assets", name="api.corporation.assets", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Method("GET")
     */
    public function indexAction(Corporation $corp)
    {

        $group = $this->getDoctrine()->getRepository('AppBundle:AssetGroup')
            ->getLatestAssetGroup($corp);


        $assets = $this->getDoctrine()->getRepository('AppBundle:Asset')
            ->findBy(['asset_group' => $group->getId()]);


        $json = $this->get('serializer')->serialize($assets, 'json');

        return $this->jsonResponse($json);

    }

}
