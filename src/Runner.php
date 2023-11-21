<?php

namespace ValeSaude\LaravelHealthCheck;

use Illuminate\Contracts\Container\Container;
use ValeSaude\LaravelHealthCheck\Config\ProfileConfig;

class Runner
{
    /** @var ProfileConfig */
    private $config;
    /** @var Container */
    private $container;

    public function __construct(ProfileConfig $config, Container $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    public function run(string ...$profiles): ResultSet
    {
        $results = [];

        foreach ($profiles as $profile) {
            $validators = $this->config->getValidatorsForProfile($profile);

            foreach ($validators as $validator) {
                $results[$validator] = $this->container->call([$validator, 'validate']);
            }
        }

        return new ResultSet($results);
    }
}