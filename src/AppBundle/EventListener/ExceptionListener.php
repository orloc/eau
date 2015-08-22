<?php

namespace AppBundle\EventListener;

use JMS\Serializer\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener {

    public function onKernelException(GetResponseForExceptionEvent $event){

        $e = $event->getException();
        $request = $event->getRequest();

        $response = new Response();

        if ($e instanceof RuntimeException && in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && strtolower($request->getContentType()) == 'json'){
            $response->setStatusCode(400)
                ->setContent(json_encode(['error' => $e->getMessage(), 'code' => 400], true));

            $event->setResponse($response);
        }
    }
}
