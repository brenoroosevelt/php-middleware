<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware;

class Bus extends MiddlewareStack implements BusInterface
{
    /** @inheritDoc */
    public function handle(object $subject): mixed
    {
        return $this->__invoke($subject);
    }
}
