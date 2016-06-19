<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        $request = $event->getRequest();

        $response = new Response();

        var_dump($e);
        if (strtolower($request->getContentType()) === 'json') {
            $code = 500;
            if ($e instanceof NotFoundHttpException) {
                $code = 404;
            }

            if ($e instanceof AccessDeniedException) {
                $code = 403;
            }
            $response->setContent(json_encode([
                'error' => $e->getMessage(),
                'code' => $code,
            ], true))
            ->setStatusCode($code);

            $event->setResponse($response);
        }
    }
}
