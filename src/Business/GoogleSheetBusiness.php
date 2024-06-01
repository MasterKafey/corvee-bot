<?php

namespace App\Business;

use App\Model\Corvee;
use App\Model\SupermarketItem;
use Google_Client;
use Google_Service_Sheets;

class GoogleSheetBusiness
{
    private ?Google_Service_Sheets $service = null;

    public function __construct(
        private readonly string $credentialsFilePath,
        private readonly string $corveeSpreadsheetId,
        private readonly string $courseListSpreadsheetId,
    )
    {
    }

    public function getService(): Google_Service_Sheets
    {
        if (null === $this->service) {
            $client = new Google_Client();
            $client->setAuthConfig($this->credentialsFilePath);
            $client->setApplicationName('Google Sheets API PHP');
            $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
            $this->service = new Google_Service_Sheets($client);
        }

        return $this->service;
    }

    /** @return Corvee[] */
    public function getCorveeList(): array
    {
        $range = 'A2:E1000';
        $service = $this->getService();
        $response = $service->spreadsheets_values->get($this->corveeSpreadsheetId, $range);

        return array_filter(array_map(function (array $corvee) {
            if (empty($corvee[0])) {
                return null;
            }
            return (new Corvee())
                ->setContent($corvee[0])
                ->setWho($corvee[1])
                ->setExecutionDate(\DateTime::createFromFormat('d/m/Y', $corvee[2])->setTime(0, 0))
                ->setImportance($corvee[3])
                ->setToDelete($corvee[4] === "TRUE");
        }, $response->getValues()));
    }

    /** @param Corvee[] $corvees */
    public function setCorveeList(array $corvees): void
    {
        $range = 'A2:E1000';
        $service = $this->getService();
        $values = [];
        for ($i = 0; $i <= 998; ++$i) {
            $corvee = $corvees[$i] ?? null;
            if (null !== $corvee) {
                $values[] = [
                    $corvee->getContent(),
                    $corvee->getWho(),
                    $corvee->getExecutionDate()->format('d/m/Y'),
                    $corvee->getImportance(),
                    $corvee->getToDelete() ? 'TRUE' : 'FALSE',
                ];
                continue;
            }

            $values[] = [
                '',
                '',
                '',
                '',
                'FALSE'
            ];
        }

        $body = new \Google_Service_Sheets_ValueRange();
        $body->setValues($values);
        $body->setRange($range);

        $service->spreadsheets_values->update($this->corveeSpreadsheetId, $range, $body, [
            'valueInputOption' => 'USER_ENTERED',
        ]);
    }

    /** @return SupermarketItem[] */
    public function getSupermarketItems(): array
    {
        $range = 'A2:E1000';
        $service = $this->getService();
        $response = $service->spreadsheets_values->get($this->courseListSpreadsheetId, $range);

        return array_filter(array_map(function (array $item) {
            if (empty($item[1])) {
                return null;
            }

            return (new SupermarketItem())
                ->setName($item[1])
                ->setQuantity($item[2] ?? null)
                ->setUnit($item[3] ?? null)
                ->setComment($item[4] ?? null)
                ->setToDelete($item[0] === "TRUE");
        }, $response->getValues()));
    }

    /** @param SupermarketItem[] $supermarketItems */
    public function setSupermarketItems(array $supermarketItems): void
    {
        $range = 'A2:E1000';
        $service = $this->getService();
        $values = [];

        for ($i = 0; $i < 998; ++$i) {
            $supermarketItem = $supermarketItems[$i] ?? null;
            if (null !== $supermarketItem) {
                $values[] = [
                    $supermarketItem->getToDelete() ? "TRUE" : "FALSE",
                    $supermarketItem->getName(),
                    $supermarketItem->getQuantity(),
                    $supermarketItem->getUnit(),
                    $supermarketItem->getComment(),
                ];
                continue;
            }

            $values[] = [
                'FALSE',
                '',
                '',
                '',
                '',
            ];
        }

        $body = new \Google_Service_Sheets_ValueRange();
        $body->setValues($values);
        $body->setRange($range);

        $service->spreadsheets_values->update($this->courseListSpreadsheetId, $range, $body, [
            'valueInputOption' => 'USER_ENTERED',
        ]);
    }
}
