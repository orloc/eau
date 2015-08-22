<?php

namespace AppBundle\EventListener;

use AppBundle\Controller\ApiControllerInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ApiRequestListener {

    private $serializer;

    public function __construct(Serializer $serializer){
        $this->serializer = $serializer;

    }

    public function onKernelController(FilterControllerEvent $event){

        $controller = $event->getController();

        if (is_array($controller)){
            $controller = $controller[0];
        }

        if ($controller instanceof ApiControllerInterface){
            $this->checkRequest($event);
        }
    }

    protected function checkRequest(FilterControllerEvent $event){
        $request = $event->getRequest();

        if (in_array($request->getMethod(), ['POST', 'PATCH']) && 0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = $this->serializer->deserialize($request->getContent(),'array' ,'json');
            $request->request->replace(is_array($data) ? $data : []);
        }
    }
}