<?php

declare(strict_types = 1);

namespace Infinityloop\Tests\ObserverComponent;

final class TObserverComponentTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testInjectEventMapperObserver() : void
    {
        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('TestPresenter:edit');

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('run')
            ->andReturn($httpResponse2);

        $presenterFactory = \Mockery::mock(\Nette\Application\IPresenterFactory::class);
        $presenterFactory->expects('createPresenter')
            ->with('TestPresenter:edit')
            ->andReturn($presenter);

        $router = \Mockery::mock(\Nette\Routing\Router::class);

        $httpRequest = \Mockery::mock(\Nette\Http\IRequest::class);
        $httpResponse = \Mockery::mock(\Nette\Http\IResponse::class);

        $application = new \Nette\Application\Application($presenterFactory, $router, $httpRequest, $httpResponse);
        $application->processRequest($request);

        $storage = \Mockery::mock(\Nette\Caching\IStorage::class);

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage);

        $decoratorInstance = new class()
        {
            use \Infinityloop\ObserverComponent\TObserverComponent;

            public static function getObservedEvents() : array
            {
                return [];
            }

            public function observableUpdated(\Infinityloop\ObserverComponent\IEvent $event) : void
            {
            }

            public function lookupPath(?string $type = null, bool $throw = true) : ?string
            {
                return null;
            }
        };
        $decoratorInstance->injectEventMapperObserver($instance);
    }
}
