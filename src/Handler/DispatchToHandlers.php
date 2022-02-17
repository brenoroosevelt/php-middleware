<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Handler;

use BrenoRoosevelt\Middleware\MiddlewareInterface;
use BrenoRoosevelt\Psr11\NullContainer;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

class DispatchToHandlers implements MiddlewareInterface
{
    private ContainerInterface $container;

    public function __construct(
        private LoaderInterface $loader,
        ?ContainerInterface $container = null
    ) {
        $this->container = $container ?? new NullContainer;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function process($subject, callable $next): mixed
    {
        $handlers = array_filter(
            $this->loader->loadHandlers(),
            fn (ParsedHandler $handler) => $handler->subject() === get_class($subject)
        );

        $fn = static fn ($s) => null;
        foreach ($handlers as $handler) {
            $method = new ReflectionMethod($handler->className(), $handler->methodName());
            $instance = $this->getInstance($method, $handler);
            $args = $this->resolveArguments($method, $subject);
            $fn = static function ($subject) use ($fn, $method, $instance, $args) {
                $fn($subject);
                return $method->invokeArgs($instance, $args);
            };
        }

        return $fn($subject);
    }

    private function getInstance(ReflectionMethod $method, ParsedHandler $handler): mixed
    {
        if ($method->isStatic()) {
            return null;
        }

        return
            $this->container->has($handler->className()) ?
                $this->container->get($handler->className()) :
                new ($handler->className());
    }

    private function resolveArguments(ReflectionMethod $method, $subject): array
    {
        $args = [];
        foreach ($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            if ($parameter->isDefaultValueAvailable()) {
                $args[$name] = $parameter->getDefaultValue();
            } elseif (!$parameter->isOptional() && $parameter->allowsNull()) {
                $args[$name] = null;
            } elseif ($parameter->isOptional()) {
                continue;
            } elseif ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $typeHint = ltrim($type->getName(), '?');
                if ($typeHint === 'self') {
                    $typeHint = $parameter->getDeclaringClass()->getName();
                }

                if ($typeHint === get_class($subject)) {
                    $args[$name] = $subject;
                    continue;
                }

                if ($this->container->has($typeHint)) {
                    $args[$name] = $this->container->get($typeHint);
                } else {
                    $args[$name] = new $typeHint;
                }
            }
        }

        return $args;
    }
}
