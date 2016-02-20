<?php

namespace AppBundle\Controller;

use EveBundle\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class AbstractController extends Controller
{
    public function getRepository($name, $manager = 'default')
    {
        $repo = $this->get('evedata.registry')
            ->get($name);

        if ($repo instanceof RepositoryInterface) {
            return $repo;
        }

        return $this->getDoctrine()->getRepository($name, $manager);
    }

    protected function jsonResponse($data, $status = 200, $headers = [])
    {
        return new Response($data, $status, array_merge([
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($data),
        ], $headers));
    }

    public function paginateResult(Request $request, $result, $noLimit = false)
    {
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate($result,
            $request->query->get('page', 1),
            $request->query->get('per_page', $request->get('per_page', $noLimit ? 1000000 : 15))
        );

        return $pagination;
    }

    protected function getErrorResponse(ConstraintViolationList $errors)
    {
        return $this->jsonResponse(
            $this->get('serializer')->serialize($errors, 'json'),
            400);
    }
}
