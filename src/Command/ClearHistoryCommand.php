<?php

namespace App\Command;

use App\Business\UserBusiness;
use Discord\DiscordCommandClient;
use Discord\Helpers\Collection;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:discord:clear-history')]
class ClearHistoryCommand extends Command
{
    private readonly DiscordCommandClient $client;

    public function __construct(
        private readonly UserBusiness $userBusiness,
        string                        $token,
        string                        $prefix,

    )
    {
        parent::__construct();
        $this->client = new DiscordCommandClient([
            'token' => $token,
            'prefix' => $prefix,
        ]);
    }

    public function configure(): void
    {
        $this->addArgument('user', InputArgument::REQUIRED, 'User to clear bot history');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $this->userBusiness->getUserId($input->getArgument('user'));
        $this->client->on('ready', function (DiscordCommandClient $discord) use ($userId) {
            $discord->users->fetch($userId)->then(function (User $user) use ($discord, $userId) {
                $user->getPrivateChannel()->done(function (Channel $privateChannel) use ($discord, $userId) {
                    $privateChannel->getMessageHistory([])->then(function (Collection $messages) use ($discord, $userId, $privateChannel) {
                        $filterMessage = array_filter($messages->toArray(), function (Message $message) use ($discord, $userId) {
                            return $message->author->id !== $userId;
                        });

                        $promises[] = array_map(function (Message $message) use ($privateChannel) {
                            return $privateChannel->messages->delete($message);
                        }, $filterMessage);

                    });
                });
            });
        });
        $this->client->run();

        return Command::SUCCESS;
    }
}