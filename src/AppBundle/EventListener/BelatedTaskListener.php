<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class BelatedTaskListener implements EventSubscriberInterface {

    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher){
        $this->dispatcher = $dispatcher;
    }

    public function onTerminate(){
        $tasks = $this->dispatcher->getDeferredEvents();
        $this->process($tasks);
    }

    protected function process(array $tasks){
        foreach ($tasks as $name => $events) {
            foreach ($events as $e){
                $this->dispatcher->dispatch($name, $e);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [ KernelEvents::TERMINATE => 'onTerminate'];
    }

}