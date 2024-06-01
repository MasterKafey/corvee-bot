<?php

namespace App\Discord\Command;

use App\Business\CorveeBusiness;
use App\Business\UserBusiness;
use App\Discord\Handler\BotCommand;
use App\Model\Corvee;
use Discord\Parts\Channel\Message;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;


#[BotCommand(
    name: 'tomorrow',
    usage: 'tomorrow.usage',
    description: 'tomorrow.description',
    longDescription: 'tomorrow.long_description'
)]
class TomorrowCommand extends AbstractCommand
{
    private CorveeBusiness $corveeBusiness;

    private UserBusiness $userBusiness;

    private TranslatorInterface $translator;

    #[Required]
    public function setCorveeBusiness(CorveeBusiness $corveeBusiness): void
    {
        $this->corveeBusiness = $corveeBusiness;
    }

    #[Required]
    public function setUserBusiness(UserBusiness $userBusiness): void
    {
        $this->userBusiness = $userBusiness;
    }

    #[Required]
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function __invoke(Message $message): void
    {
        $corvees = $this->corveeBusiness->getCorvees($this->userBusiness->getUserName($message->author->id));
        $tomorrow = new \DateTime();
        $tomorrow->add(new \DateInterval('P1D'));
        $tomorrow->setTime(0, 0);
        $corvees = array_filter($corvees, function (Corvee $corvee) use ($tomorrow) {
            return !$corvee->getToDelete() && $corvee->getExecutionDate() == $tomorrow;
        });

        $message->reply($this->getCorveesMessage($corvees));
    }

    public function getCorveesMessage(array $corvees): string|null
    {
        if (empty($corvees)) {
            return $this->translator->trans(
                id: 'tomorrow.action.corvee.empty',
                domain: 'command'
            );
        }

        return
            $this->corveeBusiness->convertMessage(
                corvees: $corvees,
                singleTitleKey: 'tomorrow.action.corvee.title.single',
                multipleTitleKey: 'tomorrow.action.corvee.title.multiple',
                descriptionKey: 'today.action.corvee.description',
            );
    }
}
