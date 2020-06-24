<?php

declare(strict_types = 1);

namespace Infinityloop\ObserverComponent;

trait TObservable
{
    private EventMapper $eventMapper;

    final public function injectEventMapperObservable(EventMapper $eventMapper) : void
    {
        $this->eventMapper = $eventMapper;
    }

    public function notifyObservers(IEvent $event) : void
    {
        $this->eventMapper->dispatchEvent($event);
    }
}
