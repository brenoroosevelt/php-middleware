<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware;

interface BusInterface
{
    /**
     * Handles object
     *
     * @param object $subject
     * @return mixed
     */
    public function handle(object $subject): mixed;
}
