<?php

namespace App\Discord\Command;

use App\Business\CorveeBusiness;
use App\Business\UserBusiness;
use App\Discord\Handler\BotCommand;
use App\Model\Corvee;
use Discord\Parts\Channel\Message;
use Symfony\Contracts\Translation\TranslatorInterface;

#[BotCommand(
    name: 'today',
    usage: '/today',
    description: 'today.description',
    longDescription: 'today.long_description'
)]
class TodayCommand implements CommandInterface
{
    public function __construct(
        private readonly CorveeBusiness      $corveeBusiness,
        private readonly UserBusiness        $userBusiness,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function __invoke(Message $message): void
    {
        $userId = $message->author->id;
        $userName = $this->userBusiness->getUserName($userId);

        $corvees = $this->corveeBusiness->getCorvees($userName);
        $corvees = array_filter($corvees, function (Corvee $corvee) {
            return !$corvee->getToDelete();
        });
        $message->reply($this->getCorveesMessage($corvees));
    }

    public function getCorveesMessage(array $corvees): string|null
    {
        if (empty($corvees)) {
            return $this->translator->trans(
                id: 'today.action.corvee.empty',
                domain: 'command'
            );
        }

        $todayCorvee = [];
        $lateCorvee = [];
        $now = (new \DateTime())->setTime(0, 0);
        foreach ($corvees as $corvee) {
            if ($corvee->getExecutionDate() == $now) {
                $todayCorvee[] = $corvee;
            } else if ($corvee->getExecutionDate() <= $now) {
                $lateCorvee[] = $corvee;
            }
        }

        return
            $this->corveeBusiness->convertMessage(
                corvees: $todayCorvee,
                singleTitleKey: 'today.action.corvee.today.single',
                multipleTitleKey: 'today.action.corvee.today.multiple',
                descriptionKey: 'today.action.corvee.description',
            )
            .
            $this->corveeBusiness->convertMessage(
                corvees: $lateCorvee,
                singleTitleKey: 'today.action.corvee.late.single',
                multipleTitleKey: 'today.action.corvee.late.multiple',
                descriptionKey: 'today.action.corvee.description',
            )
        ;
    }
}
