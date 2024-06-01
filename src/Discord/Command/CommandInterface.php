<?php

namespace App\Discord\Command;

use Discord\Parts\Channel\Message;

interface CommandInterface
{
    public function __invoke(Message $message): void;
}
