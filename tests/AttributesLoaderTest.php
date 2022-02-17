<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Tests;

use BrenoRoosevelt\Middleware\Bus;
use BrenoRoosevelt\Middleware\Handler\Attribute\Handler;
use BrenoRoosevelt\Middleware\Handler\AttributesLoader;
use BrenoRoosevelt\Middleware\Handler\DispatchToHandlers;
use BrenoRoosevelt\Middleware\Tests\Fixture\Command;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;

class AttributesLoaderTest extends TestCase
{
    public function testA()
    {
        $cache = new Psr16Cache(new ArrayAdapter());
        $attributesLoader = new AttributesLoader(Handler::class, [__DIR__], $cache);
        $bus = new Bus([new DispatchToHandlers($attributesLoader)]);
        $result = $bus->handle(new Command);
        $this->assertEquals(10, $result);
        $this->assertNotEmpty($cache->get($attributesLoader->cacheKey()));
    }
}
