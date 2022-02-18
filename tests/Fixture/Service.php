<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Tests\Fixture;

use BrenoRoosevelt\Middleware\Handler\Attribute\Handler;

class Service
{
    #[Handler(Command::class)]
    public function returnValue(Command $command, AnotherService $service): mixed
    {
        return $command->value;
    }
}
