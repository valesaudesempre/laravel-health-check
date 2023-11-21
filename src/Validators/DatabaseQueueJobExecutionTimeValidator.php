<?php

namespace ValeSaude\LaravelHealthCheck\Validators;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use ValeSaude\LaravelHealthCheck\Config\DatabaseQueueConfig;
use ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface;
use ValeSaude\LaravelHealthCheck\Result;

/**
 * @phpstan-type RunningDatabaseJob object{id: int, queue: string, payload: string, reserved_at: int, available_at: int}
 */
class DatabaseQueueJobExecutionTimeValidator implements ValidatorInterface
{
    /** @var DatabaseQueueConfig */
    private $config;

    public function __construct(DatabaseQueueConfig $config)
    {
        $this->config = $config;
    }

    public function validate(): Result
    {
        $jobs = $this->listAllRunningJobs();
        $queueMaxExecutionTime = [];

        /** @var RunningDatabaseJob $job */
        foreach ($jobs as $job) {
            if (!isset($queueMaxExecutionTime[$job->queue])) {
                $queueMaxExecutionTime[$job->queue] = $this->config->getMaxExecutionTimeForQueue($job->queue);
            }

            $maxExecutionTime = $queueMaxExecutionTime[$job->queue];
            $executionTime = $job->reserved_at - $job->available_at;

            if ($executionTime <= $maxExecutionTime) {
                continue;
            }

            return Result::unhealthy(
                "The \"{$job->queue}\" queue job {$job->id} execution time ({$executionTime}s) exceeds the maximum allowed ({$maxExecutionTime}s).",
                ['payload' => $job->payload]
            );
        }

        return Result::healthy();
    }

    private function getConnection(): ConnectionInterface
    {
        return DB::connection($this->config->getConnectionName());
    }

    /**
     * @return Collection<RunningDatabaseJob>
     */
    private function listAllRunningJobs(): Collection
    {
        return $this
            ->getConnection()
            ->table($this->config->getTableName())
            ->select(['id', 'queue', 'reserved_at', 'available_at', 'payload'])
            ->whereNotNull('reserved_at')
            ->get();
    }
}