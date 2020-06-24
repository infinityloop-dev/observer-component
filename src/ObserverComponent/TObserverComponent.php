<?php

declare(strict_types = 1);

namespace Infinityloop\ObserverComponent;

trait TObserverComponent
{
    private EventMapper $eventMapper;

    abstract public static function getObservedEvents() : array;

    abstract public function observableUpdated(IEvent $event) : void;

    final public function injectEventMapperObserver(EventMapper $eventMapper) : void
    {
        $this->eventMapper = $eventMapper;
        $this->onAnchor[] = function (\Nette\ComponentModel\IComponent $parent) : void {
            $this->eventMapper->registerObserver($this);
        };
    }
}
