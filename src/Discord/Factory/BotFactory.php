<?php

namespace App\Discord\Factory;

use Discord\Discord;
use Discord\DiscordCommandClient;

class BotFactory
{
    private static ?Discord $discord = null;

    public static function getDiscord(
        string $token,
        string $prefix,
    ): Discord
    {
        if (null === self::$discord) {
            self::$discord = self::createBot($token, $prefix);
        }

        return self::$discord;
    }

    private static function createBot(
        string $token,
        string $prefix
    ): Discord
    {
        return new DiscordCommandClient([
            'token' => $token,
            'prefix' => $prefix
        ]);
    }
}
