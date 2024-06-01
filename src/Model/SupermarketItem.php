<?php

namespace App\Model;

class SupermarketItem
{
    private string $name;

    private ?int $quantity;

    private ?string $unit;

    private ?string $comment;

    private bool $toDelete = false;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getToDelete(): bool
    {
        return $this->toDelete;
    }

    public function setToDelete(bool $toDelete): self
    {
        $this->toDelete = $toDelete;
        return $this;
    }
}
