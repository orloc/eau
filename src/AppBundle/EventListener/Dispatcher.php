<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Dispatcher extends EventDispatcher
{
    protected $deferred_events = [];

    public function addDeferred($name, Event $event)
    {
        if (!isset($this->deferred_events[$name])) {
            $this->deferred_events[$name] = [];
        }

        $this->deferred_events[$name][] = $event;
    }

    public function getDeferredEvents()
    {
        return $this->deferred_events;
    }
}
