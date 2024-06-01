<?php

namespace App\Model;

class Corvee
{
    private string $content;

    private string $who;

    private \DateTime $executionDate;

    private string $importance;

    private bool $toDelete = false;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getWho(): string
    {
        return $this->who;
    }

    public function setWho(string $who): self
    {
        $this->who = $who;
        return $this;
    }

    public function getExecutionDate(): \DateTime
    {
        return $this->executionDate;
    }

    public function setExecutionDate(\DateTime $executionDate): self
    {
        $this->executionDate = $executionDate;
        return $this;
    }

    public function getImportance(): string
    {
        return $this->importance;
    }

    public function setImportance(string $importance): self
    {
        $this->importance = $importance;
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
