<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware;

use BrenoRoosevelt\Middleware\Middlewares\CallableMiddleware;
use BrenoRoosevelt\Middleware\Middlewares\LazyMiddleware;
use BrenoRoosevelt\Psr11\NullContainer;
use Psr\Container\ContainerInterface;

class MiddlewareStack implements MiddlewareInterface
{
    /** @var array<MiddlewareInterface|callable|string> */
    private array $middlewares;

    private ContainerInterface $container;

    /**
     * @param array<MiddlewareInterface|callable|string> $middlewares
     * @param ContainerInterface|null $container
     */
    final public function __construct(array $middlewares, ?ContainerInterface $container = null)
    {
        $this->container = $container ?? new NullContainer;
        foreach ($middlewares as $middleware) {
            $this->append($middleware);
        }
    }

    public function append(MiddlewareInterface|callable|string $middleware): static
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function prepend(MiddlewareInterface|callable|string $middleware): static
    {
        array_unshift($this->middlewares, $middleware);
        return $this;
    }

    public function setContainer(ContainerInterface $container): static
    {
        $this->container = $container;
        return $this;
    }

    /** @inheritDoc */
    public function process($subject, callable $next): mixed
    {
        return $this->__invoke($subject);
    }

    public function __invoke(mixed $subject): mixed
    {
        $stack = $this->middlewares;
        if(! ($middleware = array_shift($stack))) {
            throw MiddlewareStackExhausted::missingFinalHandler();
        }

        return $this->toMiddleware($middleware)->process($subject, new self($stack, $this->container));
    }

    private function toMiddleware(MiddlewareInterface|callable|string $middleware): MiddlewareInterface
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        } elseif (is_callable($middleware)) {
            return new CallableMiddleware($middleware);
        }

        return new LazyMiddleware($middleware, $this->container);
    }

    /**
     * @param mixed $subject
     * @param array<MiddlewareInterface|callable|string> $middlewares
     * @param ContainerInterface|null $container
     * @return mixed
     */
    public static function run(mixed $subject, array $middlewares, ?ContainerInterface $container = null): mixed
    {
        return (new self($middlewares, $container))($subject);
    }
}
