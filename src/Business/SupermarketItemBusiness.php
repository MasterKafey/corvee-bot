<?php

namespace App\Business;

use App\Model\SupermarketItem;

class SupermarketItemBusiness
{
    public function __construct(
        private readonly GoogleSheetBusiness $googleSheetBusiness
    )
    {
    }

    /** @return SupermarketItem[] */
    public function getSupermarketItems(): array
    {
        $supermarketItems = $this->googleSheetBusiness->getSupermarketItems();
        return array_filter($supermarketItems, function (SupermarketItem $supermarketItem) {
            return !$supermarketItem->getToDelete();
        });
    }
}
