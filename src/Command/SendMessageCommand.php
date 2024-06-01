<?php

namespace App\Command;

use App\Business\UserBusiness;
use Discord\Builders\MessageBuilder;
use Discord\DiscordCommandClient;
use Discord\Parts\User\User;
use React\Promise\Promise;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Service\Attribute\Required;
use function React\Promise\all;

#[AsCommand(name: 'app:discord:send-message', description: 'Send a given message to a given user on discord')]
class SendMessageCommand extends Command
{
    private UserBusiness $userBusiness;
    private DiscordCommandClient $client;

    public function __construct(
        string $token,
        string $prefix,
    )
    {
        parent::__construct();
        $this->client = new DiscordCommandClient([
            'token' => $token,
            'prefix' => $prefix,
        ]);
    }

    #[Required]
    public function setUserBusiness(UserBusiness $userBusiness): void
    {
        $this->userBusiness = $userBusiness;
    }

    public function configure(): void
    {
        $this
            ->addArgument('user', InputArgument::REQUIRED, 'User name : Anne-Sophie or Jean')
            ->addArgument('message', InputArgument::REQUIRED, 'The message to send');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $messages = $input->getArgument('message');
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        $userId = $this->userBusiness->getUserId($input->getArgument('user'));
        $this->client->on('ready', function (DiscordCommandClient $discord) use ($userId, $messages) {
            $discord->users->fetch($userId)->then(function (User $user) use ($messages, $discord) {
                $promises = [];
                foreach ($messages as $message) {
                    $promises[] = $user->sendMessage($message);
                }

                all($promises)->always(function () use ($discord) {
                    $discord->close();
                });
            });
        });
        $this->client->run();

        return Command::SUCCESS;
    }
}