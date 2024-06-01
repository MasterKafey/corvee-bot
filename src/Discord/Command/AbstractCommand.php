<?php

namespace App\Discord\Command;

use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractCommand implements CommandInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    protected function trans(string $message, array $context = []): string
    {
        return $this->translator->trans($message, $context, 'command');
    }
}
