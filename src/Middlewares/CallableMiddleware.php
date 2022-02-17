<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Middlewares;

use BrenoRoosevelt\Middleware\MiddlewareInterface;

class CallableMiddleware implements MiddlewareInterface
{
    /** @var callable */
    private $fn;

    public function __construct(callable $fn)
    {
        $this->fn = $fn;
    }

    /**
     * @inheritDoc
     */
    public function process($subject, callable $next): mixed
    {
        return ($this->fn)($subject, $next);
    }
}
