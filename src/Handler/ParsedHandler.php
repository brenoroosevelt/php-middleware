<?php
declare(strict_types=1);

namespace BrenoRoosevelt\Middleware\Handler;

final class ParsedHandler
{
    public function __construct(
        private string $subject,
        private string $className,
        private string $methodName
    ) {
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function methodName(): string
    {
        return $this->methodName;
    }
}
