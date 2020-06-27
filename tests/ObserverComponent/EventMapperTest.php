<?php

declare(strict_types = 1);

namespace Infinityloop\Tests\ObserverComponent;

use Infinityloop\ObserverComponent\EventMapper;

final class EventMapperTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testComponentRegisteredReturn() : void
    {
        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('BlaPresenter:edit');

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()
            ->with(true)
            ->andReturn('edit');
        $presenter->expects('run')
            ->andReturn($httpResponse2);

        $presenterFactory = \Mockery::mock(\Nette\Application\IPresenterFactory::class);
        $presenterFactory->expects('createPresenter')
            ->with('BlaPresenter:edit')
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
            ->andReturn([0 => BlaPresenter::class]);

        $component = \Mockery::mock(Bla::class);
        $component->expects('lookupPath')
            ->once()
            ->with(\Nette\Application\IPresenter::class)
            ->andReturn(BlaPresenter::class);

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage, false);
        $instance->registerObserver($component);
    }

    public function testComponentRegisteredContinue() : void
    {
        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('BlaPresenter:edit');

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()->with(true)
            ->andReturn('edit');
        $presenter->expects('run')
            ->andReturn($httpResponse2);

        $presenterFactory = \Mockery::mock(\Nette\Application\IPresenterFactory::class);
        $presenterFactory->expects('createPresenter')
            ->with('BlaPresenter:edit')
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
            ->andReturn([0 => BlaPresenter::class, 1 => 'test']);

        $component = \Mockery::mock(Bla::class);
        $component->expects('lookupPath')
            ->with(\Nette\Application\IPresenter::class)
            ->andReturn(BlaPresenter::class);
        $component->expects('getObservedEvents')
            ->once()
            ->withNoArgs()
            ->andReturn([BlaEventEdit::class, 'shrek']);

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage);
        $instance->registerObserver($component);
    }

    public function testRegisterObserver() : void
    {
        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('BlaPresenter:edit');

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()->with(true)
            ->andReturn('edit');
        $presenter->expects('run')
            ->andReturn($httpResponse2);

        $presenterFactory = \Mockery::mock(\Nette\Application\IPresenterFactory::class);
        $presenterFactory->expects('createPresenter')
            ->with('BlaPresenter:edit')
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

        $component = \Mockery::mock(Bla::class);
        $component->expects('lookupPath')
            ->with(\Nette\Application\IPresenter::class)
            ->andReturn(Bla::class);
        $component->expects('getObservedEvents')
            ->withNoArgs()
            ->andReturn([BlaEventEdit::class]);

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage);
        $instance->registerObserver($component);
    }

    public function testDispatchEvent() : void
    {
        self::expectExceptionMessage('Kebab');

        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('BlaPresenter:edit');

        $component = new Bla();

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()
            ->with(true)
            ->andReturn('edit');
        $presenter->expects('run')
            ->andReturn($httpResponse2);
        $presenter->expects('getComponent')
            ->once()
            ->with(Bla::class)
            ->andReturn($component);

        $presenterFactory = \Mockery::mock(\Nette\Application\IPresenterFactory::class);
        $presenterFactory->expects('createPresenter')
            ->with('BlaPresenter:edit')
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
            ->andReturn([0 => Bla::class]);

        $event = new BlaEventEdit();

        $instance = new \Infinityloop\ObserverComponent\EventMapper($application, $storage);
        $instance->dispatchEvent($event);
    }

    public function testIsComponentRegistered() : void
    {
        $httpResponse2 = \Mockery::mock(\Nette\Application\IResponse::class);
        $httpResponse2->expects('send');

        $request = new \Nette\Application\Request('BlaPresenter:edit');

        $presenter = \Mockery::mock(\Nette\Application\IPresenter::class);
        $presenter->expects('getAction')
            ->once()
            ->with(true)
            ->andReturn('edit');
        $presenter->expects('run')
            ->andReturn($httpResponse2);

        $presenterFactory = \Mockery::mock(\Nette\Application\IPresenterFactory::class);
        $presenterFactory->expects('createPresenter')
            ->with('BlaPresenter:edit')
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

        $component = \Mockery::mock(Bla::class);
        $component->expects('lookupPath')
            ->with(\Nette\Application\IPresenter::class)
            ->andReturn(BlaPresenter::class);
        $component->expects('getObservedEvents')
            ->withNoArgs()
            ->andReturn([Bla::class]);

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
