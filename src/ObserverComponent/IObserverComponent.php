<?php

declare(strict_types = 1);

namespace Infinityloop\ObserverComponent;

interface IObserverComponent
{
    public static function getObservedEvents() : array;

    public function injectEventMapperObserver(EventMapper $eventMapper) : void;

    public function observableUpdated(IEvent $event) : void;

    public function lookupPath(?string $type = null, bool $throw = true) : ?string;
}
