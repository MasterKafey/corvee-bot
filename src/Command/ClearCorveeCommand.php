<?php

namespace App\Command;

use App\Business\GoogleSheetBusiness;
use App\Model\Corvee;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:corvee:clear',
    description: 'Clear corvees'
)]
class ClearCorveeCommand extends Command
{
    public function __construct(
        private readonly GoogleSheetBusiness $googleSheetBusiness,
    )
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $corvees = $this->googleSheetBusiness->getCorveeList();
        $corvees = array_values(array_filter($corvees, function (Corvee $corvee) {
            return !$corvee->getToDelete();
        }));
        usort($corvees, function (Corvee $a, Corvee $b) {
            return $a->getExecutionDate() < $b->getExecutionDate() ? -1 : 1;
        });

        $this->googleSheetBusiness->setCorveeList($corvees);

        return Command::SUCCESS;
    }
}
