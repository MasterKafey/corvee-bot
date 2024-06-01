<?php

namespace App\Business;

use Symfony\Component\Yaml\Yaml;

class UserBusiness
{
    private ?array $configuration = null;

    public function __construct(
        private readonly string $userFilePath,
    )
    {

    }

    public function setUser(string $id, string $name): void
    {
        $this->setConfiguration($name, $id);
    }

    public function getUserId(string $name): string|null
    {
        return $this->getConfiguration()[$name] ?? null;
    }

    public function getUserName(string $userId): string|null
    {
        return array_search($userId, $this->getConfiguration(), true) ?? null;
    }

    public function saveConfiguration(): void
    {
        file_put_contents($this->userFilePath, Yaml::dump($this->getConfiguration()));
    }

    public function getConfiguration(): array
    {
        if (null === $this->configuration) {
            $this->configuration = Yaml::parseFile($this->userFilePath);
        }

        return $this->configuration;
    }

    public function setConfiguration(string $key, mixed $value): void
    {
        $configuration = $this->getConfiguration();
        $configuration[$key] = $value;
        $this->configuration = $configuration;
        $this->saveConfiguration();
    }
}
