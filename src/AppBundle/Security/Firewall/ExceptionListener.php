<?php

namespace AppBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use \Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseListener;

class ExceptionListener extends BaseListener {

    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        $responseType = function() use ($event) {
            $request = $event->getRequest();
            $accepts = $request->headers->get('accept', null);
            $contentType = $request->getContentType();

            return $accepts ? $accepts : $contentType;
        };

        if ($responseType() === 'application/json'){
            $exception = $event->getException();
            $response = new JsonResponse(['code' => 403, 'message' => $exception->getMessage()], 403);

            $event->setResponse($response);
        } else {
            parent::onKernelException($event);
        }
    }
}