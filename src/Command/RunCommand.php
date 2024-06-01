<?php

namespace App\Command;

use App\Discord\Handler\CommandHandler;
use Discord\DiscordCommandClient;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Activity;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:run',
    description: 'Test command'
)]
class RunCommand extends Command
{
    public function __construct(
        private readonly DiscordCommandClient $client,
        private readonly CommandHandler       $commandHandler
    )
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->client->on('ready', function (DiscordCommandClient $discord) {
            /** @var Activity $activity */
            $activity = $discord->factory(Activity::class, [
                'name' => 'travailler sur les GDocs',
                'type' => Activity::TYPE_PLAYING
            ]);

            $discord->updatePresence($activity);
        });

        return Command::SUCCESS;
    }
}
