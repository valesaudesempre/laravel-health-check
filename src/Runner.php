<?php

namespace ValeSaude\LaravelHealthCheck;

use Illuminate\Contracts\Container\Container;
use ValeSaude\LaravelHealthCheck\Config\ProfileConfig;
use ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface;

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
                /** @var ValidatorInterface $instance */
                $instance = $this->container->make($validator);

                $results[$validator] = $instance->validate();
            }
        }

        return new ResultSet($results);
    }
}