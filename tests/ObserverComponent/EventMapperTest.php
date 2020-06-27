<?php

declare(strict_types = 1);

namespace Infinityloop\Tests\ObserverComponent;

final class EventMapperTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testComponentRegisteredReturn() : void
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
            ->withAnyArgs()
            ->andReturn([0 => TestPresenter::class]);

        $component = \Mockery::mock(TestComponent::class);
        $component->expects('lookupPath')
            ->once()
            ->with(\Nette\Application\IPresenter::class)
            ->andReturn(TestPresenter::class);

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage, false);
        $instance->registerObserver($component);
    }

    public function testComponentRegisteredContinue() : void
    {
        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('TestPresenter:edit');

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()->with(true)
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
            ->twice()
            ->withAnyArgs()
            ->andReturn([0 => TestPresenter::class, 1 => 'test']);

        $component = \Mockery::mock(TestComponent::class);
        $component->expects('lookupPath')
            ->with(\Nette\Application\IPresenter::class)
            ->andReturn(TestPresenter::class);
        $component->expects('getObservedEvents')
            ->once()
            ->withNoArgs()
            ->andReturn([TestEventEdit::class, 'shrek']);

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage);
        $instance->registerObserver($component);
    }

    public function testRegisterObserver() : void
    {
        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('TestPresenter:edit');

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()->with(true)
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
            ->twice()
            ->withAnyArgs()
            ->andReturnNull();
        $storage->expects('write')
            ->twice()
            ->withAnyArgs();

        $component = \Mockery::mock(TestComponent::class);
        $component->expects('lookupPath')
            ->with(\Nette\Application\IPresenter::class)
            ->andReturn(TestComponent::class);
        $component->expects('getObservedEvents')
            ->withNoArgs()
            ->andReturn([TestEventEdit::class]);

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage);
        $instance->registerObserver($component);
    }

    public function testDispatchEvent() : void
    {
        self::expectExceptionMessage('Kebab');

        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('TestPresenter:edit');

        $component = new \Infinityloop\Tests\ObserverComponent\TestComponent();

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()
            ->with(true)
            ->andReturn('edit');
        $presenter->expects('run')
            ->andReturn($httpResponse2);
        $presenter->expects('getComponent')
            ->once()
            ->with(TestComponent::class)
            ->andReturn($component);

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
            ->withAnyArgs()
            ->andReturn([0 => TestComponent::class]);

        $event = new \Infinityloop\Tests\ObserverComponent\TestEventEdit();

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage);
        $instance->dispatchEvent($event);
    }

    public function testIsComponentRegistered() : void
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
            ->times(3)->withAnyArgs()
            ->andReturnNull();
        $storage->expects('write')
            ->twice()
            ->withAnyArgs();

        $component = \Mockery::mock(TestComponent::class);
        $component->expects('lookupPath')
            ->with(\Nette\Application\IPresenter::class)
            ->andReturn(TestPresenter::class);
        $component->expects('getObservedEvents')
            ->withNoArgs()
            ->andReturn([TestComponent::class]);

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage, false);
        $instance->registerObserver($component);
    }

    public function testGetObserverListVisibility() : void
    {
        $reflection = new \ReflectionClass(EventMapper::class);

        self::assertTrue($reflection->getMethod('getObserverList')->isPrivate());
    }

    public function testIsComponentRegisteredVisibility() : void
    {
        $reflection = new \ReflectionClass(EventMapper::class);

        self::assertTrue($reflection->getMethod('isComponentRegistered')->isPrivate());
    }

    public function testGetEventMapVisibility() : void
    {
        $reflection = new \ReflectionClass(EventMapper::class);

        self::assertTrue($reflection->getMethod('getEventMap')->isPrivate());
    }
}
