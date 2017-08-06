<?php

namespace Maduser\Minimal\Middlewares;



abstract class AbstractMiddleware implements MiddlewareInterface
{
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
    public function before()
    {
        // TODO: Implement before() method.
    }

    /**
     *
     */
    public function after()
    {
        // TODO: Implement after() method.
    }
}