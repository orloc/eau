<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\BuybackConfiguration;
use AppBundle\Entity\Corporation;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BuybackConfigurationController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/buyback_configuration", name="api.buyback_configuration", options={"expose"=true})
     * @Method("POST")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function postAction(Request $request)
    {

        $content = $request->request->all();

        $config = new BuybackConfiguration();

        $corp = $this->getDoctrine()
            ->getRepository('AppBundle:Corporation')
            ->findOneBy(['id' => $content['corporation']]);


        if (!$corp instanceof Corporation){
            return $this->jsonResponse(['error' => 'Not found', 'code' => 400], 400);
        }

        $config->setOverride($content['override_price'])
            ->setCorporation($corp)
            ->setRegions($content['base_regions'])
            ->setType(
                $content['type'] === 'item'
                    ? BuybackConfiguration::TYPE_SINGLE
                    : BuybackConfiguration::TYPE_GLOBAL
            );

        $em = $this->getDoctrine()->getManager();

        $em->persist($config);

        $json = $this->get('serializer')->serialize($config, 'json');

        return $this->jsonResponse($json);
    }
}
