<?php

namespace ValeSaude\LaravelHealthCheck\Config;

use Illuminate\Contracts\Config\Repository;

/**
 * @codeCoverageIgnore
 */
class ProfileConfig
{
    /** @var Repository */
    protected $configRepository;

    public function __construct(Repository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * @return array<string, class-string<\ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface>[]>
     */
    public function getProfiles(): array
    {
        return $this->configRepository->get('laravel-health-check.profiles');
    }

    /**
     * @return class-string<\ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface>[]
     */
    public function getValidatorsForProfile(string $profile): array
    {
        return $this->configRepository->get("laravel-health-check.profiles.{$profile}");
    }
}