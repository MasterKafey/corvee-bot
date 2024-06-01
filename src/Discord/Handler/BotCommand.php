<?php

namespace App\Discord\Handler;

use Attribute;

#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_CLASS
)]
class BotCommand
{
    public function __construct(
        private readonly string $name,
        private readonly string $usage,
        private readonly string $description = 'default.description',
        private readonly string $longDescription = 'default.long_description',
        private readonly int    $cooldown = 0,
        private readonly string $cooldownMessage = 'default.cooldown.message',
        private readonly array  $aliases = [],
    )
    {
    }
}
