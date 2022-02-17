<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Handler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Handler
{
    public function __construct(private string $subject)
    {
    }

    public function subject(): string
    {
        return $this->subject;
    }
}
