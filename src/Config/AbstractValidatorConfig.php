<?php

namespace ValeSaude\LaravelHealthCheck\Config;

use Illuminate\Contracts\Config\Repository;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractValidatorConfig
{
    /** @var Repository */
    protected $configRepository;

    public function __construct(Repository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * @param mixed $default
     *
     * @return mixed|null
     */
    protected function getSettings(string $key, $default = null)
    {
        return $this->configRepository->get('laravel-health-check.settings.'.static::class.'.'.$key, $default);
    }
}