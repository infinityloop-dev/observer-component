<?php

declare(strict_types = 1);

namespace Infinityloop\Tests\ObserverComponent;

class Bla implements \Infinityloop\ObserverComponent\IObserverComponent
{
    use \Infinityloop\ObserverComponent\TObserverComponent;

    public static function getObservedEvents() : array
    {
        return [
            \Infinityloop\Tests\ObserverComponent\BlaEventEdit::class
        ];
    }

    public function observableUpdated(\Infinityloop\ObserverComponent\IEvent $event) : void
    {
        throw new \Exception('Kebab');
    }

    public function lookupPath(?string $type = null, bool $throw = true) : ?string
    {
        return BlaPresenter::class;
    }
}
