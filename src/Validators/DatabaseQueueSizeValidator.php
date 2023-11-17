<?php

namespace ValeSaude\LaravelHealthCheck\Validators;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use ValeSaude\LaravelHealthCheck\Config\DatabaseQueueSizeValidatorConfig;
use ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface;
use ValeSaude\LaravelHealthCheck\Result;

class DatabaseQueueSizeValidator implements ValidatorInterface
{
    /** @var DatabaseQueueSizeValidatorConfig */
    private $config;

    public function __construct(DatabaseQueueSizeValidatorConfig $config)
    {
        $this->config = $config;
    }

    public function validate(): Result
    {
        $queues = $this->listAllPendingQueues();
        $maxSize = $this->config->getMaxSize();

        foreach ($queues as $queue) {
            $currentSize = $this->getCurrentSize($queue);

            if ($currentSize <= $maxSize) {
                continue;
            }

            return Result::unhealthy("The \"{$queue}\" queue size ({$currentSize}) exceeds the maximum allowed ({$maxSize}).");
        }

        return Result::healthy();
    }

    private function getConnection(): ConnectionInterface
    {
        return DB::connection($this->config->getConnectionName());
    }

    private function listAllPendingQueues(): Collection
    {
        return $this
            ->getConnection()
            ->table($this->config->getTableName())
            ->selectRaw('DISTINCT queue AS queue_name')
            ->get()
            ->pluck('queue_name');
    }

    private function getCurrentSize(string $queue): int
    {
        return $this
            ->getConnection()
            ->table($this->config->getTableName())
            ->where('queue', $queue)
            ->count();
    }
}