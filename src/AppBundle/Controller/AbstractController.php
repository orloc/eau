<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class AbstractController extends Controller {

    public function getRepository($name, $manager = 'default'){
        // check ours

        return $this->getDoctrine()->getRepository($name, $manager);
    }

    protected function jsonResponse($data, $status = 200, $headers = []){
        return new Response($data, $status, array_merge([
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($data)
        ],$headers));

    }

    public function paginateResult(Request $request, $result){

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate($result,
            $request->query->get('page',1),
            $request->query->get('per_page', 10)
        );

        return $pagination;
    }

    public function updateResultSet($items){
        $itemTypes = $this->getRepository('EveBundle:ItemType', 'eve_data');
        $dataMapper = $this->get('app.datamapper.service');

        foreach ($items as $i){
            $updateData = $itemTypes->getItemTypeData($i->getTypeId());
            $dataMapper->updateObject($i, $updateData);
        }

        return $items;
    }

    protected function getErrorResponse(ConstraintViolationList $errors){
        return $this->jsonResponse(
            $this->get('serializer')->serialize($errors, 'json'),
            400);
    }
}