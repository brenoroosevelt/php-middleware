<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware;

use BrenoRoosevelt\Middleware\Middlewares\CallableMiddleware;
use BrenoRoosevelt\Middleware\Middlewares\ErrorMiddleware;
use BrenoRoosevelt\Middleware\Middlewares\LazyMiddleware;
use BrenoRoosevelt\Psr11\NullContainer;
use Psr\Container\ContainerInterface;
use RuntimeException;

class MiddlewareStack implements MiddlewareInterface
{
    /** @var MiddlewareInterface[] */
    private array $middlewares;
    private ContainerInterface $container;
    private MiddlewareInterface $stackExhaustedStrategy;

    final public function __construct(
        array $middlewares,
        ?ContainerInterface $container = null,
        ?MiddlewareInterface $stackExhaustedStrategy = null
    ) {
        $this->container = $container ?? new NullContainer;
        $this->middlewares = array_map(fn($m) => self::toMiddleware($m, $this->container), $middlewares);
        $this->stackExhaustedStrategy =
            $stackExhaustedStrategy ??
            new ErrorMiddleware(
                new RuntimeException('Middleware stack exhausted, missing final response middleware')
            );
    }

    public function setStackExhaustedStrategy(MiddlewareInterface $middleware)
    {
        $this->stackExhaustedStrategy = $middleware;
    }

    public function prepend(MiddlewareInterface|callable|string $middleware): self
    {
        array_unshift($this->middlewares, self::toMiddleware($middleware, $this->container));
        return $this;
    }

    public function append(MiddlewareInterface|callable|string $middleware): self
    {
        $this->middlewares[] = self::toMiddleware($middleware, $this->container);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process($subject, callable $next): mixed
    {
        return $this->__invoke($subject);
    }

    public function __invoke($subject)
    {
        $stack = $this->middlewares;
        $middleware = $stack[0] ?? $this->stackExhaustedStrategy;
        array_shift($stack);

        return $middleware->process($subject, new self($stack, $this->container));
    }

    public static function run(
        $subject,
        array $middlewares,
        ?ContainerInterface $container = null,
        ?MiddlewareInterface $stackExhaustedStrategy = null
    ) {
        return (new self($middlewares, $container, $stackExhaustedStrategy))($subject);
    }

    private static function toMiddleware(
        MiddlewareInterface|callable|string $middleware,
        ?ContainerInterface $container = null
    ): MiddlewareInterface {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        } elseif (is_callable($middleware)) {
            return new CallableMiddleware($middleware);
        }

        return new LazyMiddleware($middleware, $container);
    }
}
