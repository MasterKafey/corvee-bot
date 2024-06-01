<?php

namespace App\MessageHandler\Message;

class ExecuteCommandMessage
{
    public function __construct(
        private readonly string $command,
        private readonly array $parameters = [],
    )
    {
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
