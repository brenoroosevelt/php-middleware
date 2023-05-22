<?php

declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Middlewares;

use BrenoRoosevelt\Middleware\MiddlewareStackExhausted;

/**
 * @readonly
 * @template Input
 * @template Output
 */
class Stack
{
    /**
     * @readonly
     * @var array<callable(Input $input, callable $next): Output>
     */
    private array $handlers;

    /**
     * @param callable(Input $input, callable $next): Output ...$handlers
     */
    final public function __construct(callable ...$handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @param callable(Input $input, callable $next): Output $handler
     * @return Stack
     */
    public function append(callable $handler): self
    {
        return new self(...$this->handlers, $handler);
    }

    /**
     * @param callable(Input $input, callable $next): Output $handler
     * @return Stack
     */
    public function prepend(callable $handler): self
    {
        return new self($handler, ...$this->handlers);
    }

    /**
     * @param Input $input
     * @return Output
     */
    public function process(mixed $input): mixed
    {
        $handlers = $this->handlers;
        $handler = array_shift($handlers) ?? throw MiddlewareStackExhausted::missingFinalHandler();

        return $handler($input, new self(...$handlers));
    }

    /**
     * @param Input $input
     * @return Output
     */
    public function __invoke(mixed $input): mixed
    {
        return $this->process($input);
    }
}
