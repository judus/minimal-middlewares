<?php

namespace Maduser\Minimal\Middlewares\Tests;

use Maduser\Minimal\Middlewares\AbstractMiddleware;
use Maduser\Minimal\Middlewares\Middleware;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    public function testConstructor()
    {
        $middleware = new Middleware([]);
    }

    public function testSetAndGetMiddlewares()
    {
        $middleware = new Middleware([]);
        $middleware->setMiddlewares(['dummy1', 'dummy2']);

        $result = $middleware->getMiddlewares();
        $expected = ['dummy1', 'dummy2'];

        $this->assertEquals($expected, $result);
    }

    public function testExecuteWithBeforeReturningTrue()
    {
        $action = new DummyActionA();
        $middleware = new Middleware([DummyMiddlewareA::class]);

        $result = $middleware->dispatch(function () use ($action) {
            return $action->action();
        });

        $this->assertEquals('dummy', $result);
    }

    public function testExecuteWithBeforeReturningFalse()
    {
        $action = new DummyActionB();
        $middleware = new Middleware([DummyMiddlewareB::class]);

        $result = $middleware->dispatch(function () use ($action) {
            $action->action();
        });

        $this->assertNull($result);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage dummy
     */
    public function testExecuteBeforeExecutesAction()
    {
        $action = new DummyActionA();
        $middleware = new Middleware([DummyMiddlewareC::class]);

        $result = $middleware->dispatch(function () use ($action) {
            return $action->action();
        });
    }

    public function testExecuteAfterExecutesAction()
    {
        $action = new DummyActionA();
        $middleware = new Middleware([DummyMiddlewareD::class]);

        $result = $middleware->dispatch(function () use ($action) {
            return $action->action();
        });

        $this->assertEquals('test', $result);
    }

    public function testExecuteSeveralMiddlewares()
    {
        $action = new DummyActionA();
        $middleware = new Middleware([
            DummyMiddlewareE::class,
            DummyMiddlewareD::class
        ]);

        $result = $middleware->dispatch(function () use ($action) {
            return $action->action();
        });

        $this->assertEquals('testDummy', $result);

    }
}


class DummyActionA
{
    public function action()
    {
        return 'dummy';
    }
}

class DummyActionB
{
    public function action()
    {
        throw new \Exception('This should not have been executed');
    }
}

class DummyMiddlewareA extends AbstractMiddleware
{
    public function before()
    {
        return true;
    }

    public function after()
    {
        return true;
    }
}

class DummyMiddlewareB extends AbstractMiddleware
{
    public function before()
    {
        return false;
    }

    public function after()
    {
        return true;
    }
}

class DummyMiddlewareC extends AbstractMiddleware
{
    public function before()
    {
        throw new \Exception('dummy');
    }

    public function after()
    {
        return true;
    }
}

class DummyMiddlewareD extends AbstractMiddleware
{
    public function before()
    {
        return true;
    }

    public function after()
    {
        $payload = $this->getPayload();

        $payload = str_replace('dummy', 'test', $payload);

        $this->setPayload($payload);

        return true;
    }
}

class DummyMiddlewareE extends AbstractMiddleware
{
    public function before()
    {
        return true;
    }

    public function after()
    {
        $payload = $this->getPayload();

        $payload = str_replace('test', 'testDummy', $payload);

        $this->setPayload($payload);

        return true;
    }
}