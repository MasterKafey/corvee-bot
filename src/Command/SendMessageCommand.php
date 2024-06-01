<?php

namespace App\Command;

use App\Business\UserBusiness;
use Discord\DiscordCommandClient;
use Discord\Parts\User\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Service\Attribute\Required;

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
        $message = $input->getArgument('message');
        $userId = $this->userBusiness->getUserId($input->getArgument('user'));
        $this->client->on('ready', function (DiscordCommandClient $discord) use ($userId, $message) {
            $discord->users->fetch($userId)->then(function (User $user) use ($message, $discord) {
                $user->sendMessage($message)->always(function () use ($discord) {
                    $discord->close();
                });
            });
        });
        $this->client->run();

        return Command::SUCCESS;
    }
}