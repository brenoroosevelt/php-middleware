<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Middlewares;

use BrenoRoosevelt\Middleware\MiddlewareInterface;

class DummyMiddleware implements MiddlewareInterface
{
    public function __construct(private mixed $value = null)
    {
    }

    public function process($subject, callable $next): mixed
    {
        return $this->value;
    }
}
