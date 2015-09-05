<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    protected function getErrorResponse(ConstraintViolationList $errors){
        return $this->jsonResponse(
            $this->get('serializer')->serialize($errors, 'json'),
            400);
    }
}