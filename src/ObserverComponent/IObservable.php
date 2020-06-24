<?php

declare(strict_types = 1);

namespace Infinityloop\ObserverComponent;

interface IObservable
{
    public function injectEventMapperObservable(EventMapper $eventMapper) : void;

    public function notifyObservers(IEvent $event) : void;
}
