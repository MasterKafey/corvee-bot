<?php

namespace App\Business;

use App\Model\Corvee;
use Symfony\Contracts\Translation\TranslatorInterface;

class CorveeBusiness
{
    public const TOGETHER_NAME = 'Tout les deux';

    public function __construct(
        private readonly GoogleSheetBusiness $googleSheetBusiness,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function getCorvees(string $name = null): array
    {
        $corvees = $this->googleSheetBusiness->getCorveeList();

        return array_filter($corvees, function (Corvee $corvee) use ($name) {
            return ($name === null || $name === $corvee->getWho() || self::TOGETHER_NAME === $corvee->getWho());
        });
    }


    /** @param Corvee[] $corvees */
    public function convertMessage(array $corvees, string $singleTitleKey, string $multipleTitleKey, string $descriptionKey): string
    {
        if (empty($corvees)) {
            return '';
        }

        $title = $this->translator->trans(
            id: count($corvees) === 1 ? $singleTitleKey : $multipleTitleKey,
            parameters: [
                '%corvee_number%' => count($corvees),
            ],
            domain: 'command',
        );

        $descriptions = [];
        foreach ($corvees as $corvee) {
            $descriptions[] = $this->translator->trans(
                id: $descriptionKey,
                parameters: [
                    '%content%' => $corvee->getContent(),
                    '%together%' => $corvee->getWho() === CorveeBusiness::TOGETHER_NAME ? " ({$corvee->getWho()})" : '',
                ],
                domain: 'command',
            );
        }

        return $title . "\n" . implode("\n", $descriptions) . "\n";
    }
}