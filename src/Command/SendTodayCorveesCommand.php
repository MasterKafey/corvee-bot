<?php

namespace App\Command;

use App\Business\CorveeBusiness;
use App\Business\DiscordBusiness;
use App\Model\Corvee;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:discord:send-today-corvees')]
class SendTodayCorveesCommand extends Command
{

    public function __construct(
        private readonly CorveeBusiness  $corveeBusiness,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('user', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $corvees = $this->corveeBusiness->getCorvees($input->getArgument('user'));
        $now = (new \DateTime())->setTime(0, 0);
        $corvees = array_filter($corvees, function (Corvee $corvee) use ($now) {
            return !$corvee->getToDelete() && $corvee->getExecutionDate() <= $now;
        });

        if (empty($corvees)) {
            return Command::SUCCESS;
        }

        $todayCorvee = [];
        $lateCorvee = [];
        $futureCorvee = [];
        $now = (new \DateTime())->setTime(0, 0);
        foreach ($corvees as $corvee) {
            if ($corvee->getExecutionDate() == $now) {
                $todayCorvee[] = $corvee;
            } else if ($corvee->getExecutionDate() <= $now) {
                $lateCorvee[] = $corvee;
            } else {
                $futureCorvee[] = $corvee;
            }
        }

        $message =
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
            .
            $this->corveeBusiness->convertMessage(
                corvees: $futureCorvee,
                singleTitleKey: 'today.action.corvee.future.single',
                multipleTitleKey: 'today.action.corvee.future.multiple',
                descriptionKey: 'today.action.corvee.description',
            );

        $command = $this->getApplication()->find('app:discord:send-message');
        $arguments = [
            'user' => $input->getArgument('user'),
            'message' => $message,
        ];

        return $command->run(new ArrayInput($arguments), $output);
    }
}
