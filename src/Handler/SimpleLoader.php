<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Handler;

class SimpleLoader implements LoaderInterface
{
    /** @var ParsedHandler[] */
    private array $parsedHandlers;

    public function __construct(ParsedHandler ...$parsedHandlers)
    {
        $this->parsedHandlers = $parsedHandlers;
    }

    public function loadHandlers(): array
    {
        return $this->parsedHandlers;
    }
}
