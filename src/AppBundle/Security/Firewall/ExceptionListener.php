<?php

namespace AppBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseListener;

class ExceptionListener extends BaseListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $responseType = function () use ($event) {
            $request = $event->getRequest();
            $accepts = $request->headers->get('accept', null);
            $contentType = $request->getContentType();

            return $accepts ? $accepts : $contentType;
        };

        if (strstr($responseType(), 'application/json') !== false) {
            $e = $event->getException();
            $response = new JsonResponse();
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
        } else {
            parent::onKernelException($event);
        }
    }
}
