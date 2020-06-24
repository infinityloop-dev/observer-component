<?php

declare(strict_types = 1);

namespace Infinityloop\ObserverComponent;

final class EventMapper
{
    use Nette\SmartObject;

    private \Nette\Application\Application $application;
    private \Nette\Caching\IStorage $storage;
    private ?\Nette\Caching\Cache $eventMap = null;
    private bool $debugMode;

    public function __construct(
        \Nette\Application\Application $application,
        \Nette\Caching\IStorage $storage,
        bool $debugMode
    )
    {
        $this->application = $application;
        $this->storage = $storage;
        $this->debugMode = $debugMode;
    }

    public function registerObserver(IObserverComponent $component) : void
    {
        $componentPath = $component->lookupPath(\Nette\Application\IPresenter::class);
        \assert(\is_string($componentPath));

        if (!$this->debugMode && $this->isComponentRegistered($componentPath)) {
            return;
        }

        foreach ($component::getObservedEvents() as $eventName) {
            \assert(\is_string($eventName) && \class_exists($eventName));

            $observerList = $this->getObserverList($eventName);

            if (\in_array($componentPath, $observerList, true)) {
                continue;
            }

            $observerList[] = $componentPath;
            $this->getEventMap()->save($eventName, $observerList);

            $registeredComponents = $this->getEventMap()->load('components') ?? [];
            $registeredComponents[] = $componentPath;
            $this->getEventMap()->save('components', $registeredComponents);
        }
    }

    public function dispatchEvent(IEvent $event) : void
    {
        $presenter = $this->application->getPresenter();
        \assert($presenter instanceof \Nette\Application\UI\Control);

        foreach ($this->getObserverList(\get_class($event)) as $observerPath) {
            \assert(\is_string($observerPath));

            $observer = $presenter->getComponent($observerPath);
            \assert($observer instanceof IObserverComponent);

            $observer->observableUpdated($event);
        }
    }

    private function getObserverList(string $eventName) : array
    {
        return $this->getEventMap()->load($eventName)
            ?? [];
    }

    private function isComponentRegistered(string $componentPath) : bool
    {
        $registeredComponents = $this->getEventMap()->load('components') ?? [];

        return \in_array($componentPath, $registeredComponents, true);
    }

    private function getEventMap() : \Nette\Caching\Cache
    {
        if (!$this->eventMap instanceof \Nette\Caching\Cache) {
            $applicationPath = \ltrim($this->application->getPresenter()->getAction(true), ':');
            $this->eventMap = new \Nette\Caching\Cache($this->storage, 'eventMapper-' . $applicationPath);
        }

        return $this->eventMap;
    }
}
