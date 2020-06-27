<?php

declare(strict_types = 1);

namespace Infinityloop\Tests\ObserverComponent;

final class TObservableTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testInjectEventMapperObservable() : void
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

        $decoratorInstance = new class ()
        {
            use \Infinityloop\ObserverComponent\TObservable;
        };
        $decoratorInstance->injectEventMapperObservable($instance);
    }

    public function testNotifyObservers() : void
    {
        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('TestPresenter:edit');

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()
            ->with(true)
            ->andReturn('edit');
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
        $storage->expects('read')
            ->once()
            ->andReturnNull();

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage);

        $event = new \Infinityloop\Tests\ObserverComponent\TestEventEdit();

        $decoratorInstance = new class ()
        {
            use \Infinityloop\ObserverComponent\TObservable;
        };
        $decoratorInstance->injectEventMapperObservable($instance);
        $decoratorInstance->notifyObservers($event);
    }
}
