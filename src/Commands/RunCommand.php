<?php

namespace ValeSaude\LaravelHealthCheck\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputOption;
use ValeSaude\LaravelHealthCheck\Config\ProfileConfig;
use ValeSaude\LaravelHealthCheck\Events\UnhealthyApplicationComponentsEvent;
use ValeSaude\LaravelHealthCheck\Runner;

class RunCommand extends Command
{
    protected $name = 'health-check:run';
    protected $description = 'Run the health check.';

    public function handle(ProfileConfig $config, Runner $runner): int
    {
        $profiles = $this->option('profile')
            ? Arr::wrap($this->option('profile'))
            : array_keys($config->getProfiles());

        $resultSet = $runner->run(...$profiles);

        if (!$resultSet->isHealthy()) {
            event(new UnhealthyApplicationComponentsEvent($resultSet));

            return 1;
        }

        return 0;
    }

    /**
     * @return array<int, mixed[]>
     */
    protected function getOptions(): array
    {
        return [
            [
                'profile',
                'p',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'The profile to run.',
                null,
            ],
        ];
    }
}