<?php

namespace App\MessageHandler\Handler;

use App\MessageHandler\Message\ExecuteCommandMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Process;

#[AsMessageHandler]
class ExecuteCommandMessageHandler
{
    public function __construct()
    {
    }

    public function __invoke(ExecuteCommandMessage $message): void
    {
        $process = new Process(['/usr/local/bin/php', '/app/bin/console', $message->getCommand(), ...$message->getParameters()]);
        $process->run();
    }
}
