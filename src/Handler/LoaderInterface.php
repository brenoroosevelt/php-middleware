<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Handler;

interface LoaderInterface
{
    /** @return ParsedHandler[] */
    public function loadHandlers(): array;
}
