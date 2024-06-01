<?php

namespace App\Command;

use App\Business\GoogleSheetBusiness;
use App\Model\SupermarketItem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:supermarket-item:clear',
    description: 'Clear supermarket items'
)]
class ClearSupermarketItemCommand extends Command
{
    public function __construct(
        private readonly GoogleSheetBusiness $googleSheetBusiness,
    )
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $supermarketItems = $this->googleSheetBusiness->getSupermarketItems();

        $supermarketItems = array_filter($supermarketItems, function (SupermarketItem $supermarketItem) {
            return !$supermarketItem->getToDelete();
        });

        $this->googleSheetBusiness->setSupermarketItems($supermarketItems);
        return Command::SUCCESS;
    }
}
