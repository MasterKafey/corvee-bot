<?php

namespace App\Discord\Command;

use App\Business\SupermarketItemBusiness;
use App\Discord\Handler\BotCommand;
use App\Model\SupermarketItem;
use Discord\Parts\Channel\Message;
use Symfony\Contracts\Service\Attribute\Required;

#[BotCommand(
    name: 'get-supermarket-item',
    usage: 'get-supermarket-item.usage',
    description: 'get-supermarket-item.description',
    longDescription: 'get-supermarket-item.long_description',
    aliases: ['gsi']
)]
class GetSupermarketItemCommand extends AbstractCommand
{
    private SupermarketItemBusiness $supermarketItemBusiness;

    #[Required]
    public function setSupermarketItemBusiness(SupermarketItemBusiness $supermarketItem): void
    {
        $this->supermarketItemBusiness = $supermarketItem;
    }

    public function __invoke(Message $message): void
    {
        $items = $this->supermarketItemBusiness->getSupermarketItems();

        if (empty($items)) {
            $message->reply($this->trans('get-supermarket-item.action.empty'));
            return;
        }

        $title = $this->trans('get-supermarket-item.action.title');

        $itemsDescription = [];
        foreach ($items as $item) {
            $itemsDescription[] = $this->trans(
                'get-supermarket-item.action.item',
                [
                    '%name%' => $item->getName(),
                    '%comment%' => $item->getComment(),
                    '%quantity%' => $item->getQuantity(),
                    '%unit%' => $item->getUnit(),
                ]
            );
        }

        $message->reply($title . implode("\n", $itemsDescription));
    }
}
