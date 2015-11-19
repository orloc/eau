<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractController;
use AppBundle\Controller\ApiControllerInterface;
use AppBundle\Entity\Corporation;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Starbase controller.
 */
class StarbaseController extends AbstractController implements ApiControllerInterface {

    /**
     * @Route("/corporation/{id}/starbases", name="api.corporation.starbases", options={"expose"=true})
     * @ParamConverter(name="corp", class="AppBundle:Corporation")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     */
    public function indexAction(Request $request, Corporation $corp)
    {

        $stations = $this->getDoctrine()->getRepository('AppBundle:Starbase')
            ->findBy(['corporation' => $corp]);

        $loctionRepo = $this->get('app.itemdetail.manager');

        $typeRepo = $this->get('evedata.registry')->get('EveBundle:ItemType');
        foreach ($stations as $s){
            $descriptors = array_merge(
                $loctionRepo->determineLocationDetails($s->getLocationId()),
                $typeRepo->getItemTypeData($s->getTypeId()),
                [
                    'fuel' => array_map(function($d) use ($typeRepo){
                        $data = $typeRepo->getItemTypeData($d['typeID']);

                        return ['type' => $data, 'typeID' => $d['typeID'],'quantity' => $d['quantity']];
                    }, $s->getFuel())
                ]
            );

            $s->setDescriptors($descriptors);
        }

        $json = $this->get('serializer')->serialize($stations, 'json');

        return $this->jsonResponse($json);

    }

}
