<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\BuybackConfiguration;
use AppBundle\Entity\Corporation;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BuybackConfigurationController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/buyback_configuration", name="api.buyback_configuration", options={"expose"=true})
     * @Method("GET")
     * @Secure(roles="ROLE_CEO")
     */
    public function getAllAction(Request $request)
    {

        $configs = $this->getDoctrine()
            ->getRepository('AppBundle:BuybackConfiguration')
            ->findAll();

        $json = $this->get('serializer')->serialize($configs, 'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/buyback_configuration", name="api.buyback_configuration.new", options={"expose"=true})
     * @Method("POST")
     * @Secure(roles="ROLE_CEO")
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

        $config->setCorporation($corp);

        $config->setOverride($content['override_price'])
            ->setRegions(count($content['base_regions']) ? $content['base_regions'] : null)
            ->setSingleItem($content['search_item'] != null
                ? (int)$content['search_item']
                : null)
            ->setBaseMarkdown($content['base_markdown'] != null
                ? $content['base_markdown']
                : null)
            ->setType($content['type'] === 'item'
                    ?  BuybackConfiguration::TYPE_SINGLE
                    : ($content['type'] === 'global'
                        ? BuybackConfiguration::TYPE_GLOBAL
                        : BuybackConfiguration::TYPE_REGION
                ));

        $em = $this->getDoctrine()->getManager();
        $em->persist($config);

        $em->flush();

        $json = $this->get('serializer')->serialize($config, 'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/buyback_configuration/{id}", name="api.buyback_configuration.patch", options={"expose"=true})
     * @ParamConverter(name="config", class="AppBundle:BuybackConfiguration")
     * @Method("PATCH")
     * @Secure(roles="ROLE_CEO")
     */
    public function patchAction(Request $request, BuybackConfiguration $config)
    {

        $content = $request->request->all();

        if ($config->getType() == BuybackConfiguration::TYPE_GLOBAL){
            $config->setBaseMarkdown($content['base_markdown']);
        } elseif ($config->getType() == BuybackConfiguration::TYPE_SINGLE){
            $config->setOverride($content['override']);
        } elseif ($config->getType() == BuybackConfiguration::TYPE_REGION){
            $config->setRegions($content['base_regions']);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($config);

        $em->flush();

        $json = $this->get('serializer')->serialize($config, 'json');

        return $this->jsonResponse($json);
    }

    /**
     * @Route("/buyback_configuration/{id}", name="api.buyback_configuration.delete", options={"expose"=true})
     * @ParamConverter(name="config", class="AppBundle:BuybackConfiguration")
     * @Method("DELETE")
     * @Secure(roles="ROLE_CEO")
     */
    public function deleteAction(Request $request, BuybackConfiguration $config){

        $em = $this->getDoctrine()->getManager();
        $em->remove($config);
        $em->flush();

        $json = $this->get('serializer')->serialize($config, 'json');

        return $this->jsonResponse($json);

    }
}
