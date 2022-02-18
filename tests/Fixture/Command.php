<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Tests\Fixture;

class Command
{
    public function __construct(public mixed $value)
    {
    }
}
