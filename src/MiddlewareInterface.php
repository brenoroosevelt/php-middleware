<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware;

interface MiddlewareInterface
{
    /**
     * Middleware pattern
     *
     * @param $subject
     * @param callable $next
     * @return mixed
     */
    public function process($subject, callable $next): mixed;
}
