<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends Controller {

    protected function jsonResponse($data, $status = 200, $headers = []){
        return new Response($data, $status, array_merge([
            'Content-Type' => 'application/json'
        ],$headers));

    }
}