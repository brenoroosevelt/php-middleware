<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware;

use RuntimeException;

class MiddlewareStackExhausted extends RuntimeException
{
    public static function missingFinalHandler(): MiddlewareStackExhausted
    {
        return new MiddlewareStackExhausted('Missing final handler');
    }
}
