<?php

namespace App\Discord\Command;

use App\Business\UserBusiness;
use App\Discord\Handler\BotCommand;
use Discord\Parts\Channel\Message;
use Symfony\Contracts\Service\Attribute\Required;

#[BotCommand(
    name: 'set-user',
    usage: 'set-user.usage',
    description: 'set-user.description',
    longDescription: 'set-user.long_description',
    aliases: ['su']
)]
class SetUserCommand extends AbstractCommand
{
    private const ANNE_SOPHIE = 'Anne-Sophie';
    private const JEAN = 'Jean';

    private UserBusiness $userBusiness;

    #[Required]
    public function setUserBusiness(UserBusiness $userBusiness): void
    {
        $this->userBusiness = $userBusiness;
    }

    public function __invoke(Message $message): void
    {
        $arguments = explode(' ', $message->content);

        if (count($arguments) !== 2) {
            $message->reply($this->trans('set-user.invalid_usage'));
            return;
        }

        $name = $arguments[1];

        if (!in_array($name, [self::ANNE_SOPHIE, self::JEAN])) {
            $message->reply($this->trans('set-user.invalid_name', [
                '%name%' => $name
            ]));
            return;
        }

        $this->userBusiness->setUser($message->author->id, $name);

        $message->reply($this->trans('set-user.success', [
            '%name%' => $name,
        ]));
    }
}
