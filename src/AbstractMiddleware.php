<?php namespace Maduser\Minimal\Middlewares;

use Maduser\Minimal\Middlewares\Contracts\MiddlewareInterface;

/**
 * Class AbstractMiddleware
 *
 * @package Maduser\Minimal\Middlewares
 */
abstract class AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @var
     */
    protected $payload;

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $response
     *
     * @return AbstractMiddleware
     */
    public function setPayload($response)
    {
        $this->payload = $response;

        return $this;
    }

    /**
     *
     */
    public function before() {}

    /**
     *
     */
    public function after() {}
}