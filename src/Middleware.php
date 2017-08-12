<?php namespace Maduser\Minimal\Middlewares;

use Maduser\Minimal\Framework\Facades\IOC;
use Maduser\Minimal\Middlewares\Contracts\MiddlewareInterface;
use Maduser\Minimal\Provider\Contracts\ProviderInterface;

/**
 * Class Middleware
 *
 * @package Maduser\Minimal\Core
 */
class Middleware extends AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @var array
     */
    private $middlewares = [];

    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param array $middlewares
     */
    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Middleware constructor.
     *
     * @param ProviderInterface $provider
     * @param array             $middlewares
     */
    public function __construct(ProviderInterface $provider, array $middlewares)
    {
        $this->provider = $provider;
        $this->setMiddlewares($middlewares);
    }

    /**
     * @param \Closure $task
     * @param null     $middlewares
     *
     * @return mixed
     */
    public function dispatch(\Closure $task, $middlewares = null)
    {
        is_array($middlewares) || $middlewares = $this->getMiddlewares();

        $beforeReturnValue = $this->before($middlewares);

        if ($beforeReturnValue === false || $beforeReturnValue !== true) {
            return $beforeReturnValue;
        }

        $this->setPayload($task());

        $afterReturnValue = $this->after(array_reverse($middlewares));

        if ($afterReturnValue === false || $afterReturnValue !== true) {
            return $afterReturnValue;
        }

        return $this->getPayload();
    }

    /**
     * @param array $middlewares
     *
     * @return bool
     */
    public function before($middlewares = null)
    {
        return $this->execute('before', $middlewares);
    }

    /**
     * @param array $middlewares
     * @param       $response
     *
     * @return bool
     */
    public function after($middlewares = null, $response = null)
    {
        return $this->execute('after', $middlewares, $response);
    }

    protected function execute($when, $middlewares, $response = null)
    {
        foreach ($middlewares as $key => $middleware) {

            $parameters = [];

            if (is_array($middleware)) {
                $parameters = $middleware;
                $middleware = $key;
            }
            if (class_exists($middleware)) {
                if ($response) {
                    array_push($parameters, $response);
                }

                /** @var AbstractMiddleware $middleware */

                $middleware = $this->provider->make($middleware, $parameters);

                $middleware->setPayload($this->getPayload());
                $returnValue = $middleware->{$when}($this);

                $this->setPayload($middleware->getPayload());


                if (!is_null($returnValue) &&
                    ($returnValue === false || $returnValue !== true)
                ) {
                    return $this->getPayload();
                }
            }
        }

        return true;
    }


}