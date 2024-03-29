<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Middlewares;

use BrenoRoosevelt\Middleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;

class LazyMiddleware implements MiddlewareInterface
{
    private ?ContainerInterface $container;

    public function __construct(private string $middlewareFQCN, ?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /** @inheritDoc */
    public function process($subject, callable $next): mixed
    {
        return $this->getMiddleware()->process($subject, $next);
    }

    private function getMiddleware(): MiddlewareInterface
    {
        if ($this->container?->has($this->middlewareFQCN)) {
            return $this->container->get($this->middlewareFQCN);
        }

        return new $this->middlewareFQCN;
    }
}
