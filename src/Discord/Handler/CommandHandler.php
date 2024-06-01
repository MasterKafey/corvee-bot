<?php

namespace App\Discord\Handler;

use App\Discord\Command\CommandInterface;
use Discord\DiscordCommandClient;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommandHandler
{
    public function __construct(
        private readonly DiscordCommandClient $discord,
        private readonly TranslatorInterface  $translator,
    )
    {
    }

    public function addCommand(CommandInterface $command): void
    {
        $reflectionClass = new \ReflectionClass($command);
        $attributes = $reflectionClass->getAttributes(BotCommand::class);

        if (empty($attributes)) {
            return;
        }

        foreach ($attributes as $attribute) {
            $options = $attribute->getArguments();
            $name = $options['name'];
            unset($options['name']);

            foreach ($options as &$option) {
                if (!is_string($option)) {
                    continue;
                }
                $option = $this->translator->trans($option, [], 'command');
            }

            $this->discord->registerCommand($name, $command, $options);
        }
    }
}
