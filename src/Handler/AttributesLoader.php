<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Handler;

use BrenoRoosevelt\Middleware\Handler\Attribute\Handler;
use FlexFqcnFinder\Fqcn;
use JsonException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class AttributesLoader implements LoaderInterface
{
    const PREFIX = 'attributes_handler_cache';

    /** @var string[] */
    private array $directories;

    public function __construct(
        private string $attribute,
        array $directories,
        private ?CacheInterface $cache = null,
        private int $attributeFlags = ReflectionAttribute::IS_INSTANCEOF
    ) {
        $this->directories = array_filter($directories, 'is_string');
    }

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function loadHandlers(): array
    {
        $cacheKey = $this->cacheKey();
        $handlers = $this->cache?->get($cacheKey);
        if (null !== $handlers) {
            return $handlers;
        }

        $finder = Fqcn::new();
        array_map(fn($dir) => $finder->addDirectory($dir, true), $this->directories);
        $handlers = $this->getHandlersFromClasses(...$finder->find());
        $this->cache?->set($cacheKey, $handlers);

        return $handlers;
    }

    /**
     * @throws ReflectionException
     */
    private function getHandlersFromClasses(string ...$classes): array
    {
        $handlers = [];
        foreach ($classes as $class) {
            $methods = (new ReflectionClass($class))->getMethods();
            foreach ($methods as $method) {
                foreach ($method->getAttributes($this->attribute, $this->attributeFlags) as $attribute) {
                    /** @var Handler $handler */
                    $handler = $attribute->newInstance();
                    $handlers[] = new ParsedHandler($handler->subject(), $class, $method->getName());
                }
            }
        }

        return $handlers;
    }

    /**
     * @throws JsonException
     */
    public function cacheKey(): string
    {
        return sha1(
            sprintf(
                '%s.%s.%s',
                self::PREFIX,
                $this->attribute,
                json_encode($this->directories, flags: JSON_THROW_ON_ERROR)
            )
        );
    }
}
