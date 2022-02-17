<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Middlewares;

use BrenoRoosevelt\Middleware\MiddlewareInterface;
use Throwable;

class ErrorMiddleware implements MiddlewareInterface
{
    public function __construct(private Throwable $throwable)
    {
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function process($subject, callable $next): mixed
    {
        throw $this->throwable;
    }
}
