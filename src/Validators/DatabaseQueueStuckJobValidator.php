<?php

namespace ValeSaude\LaravelHealthCheck\Validators;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use ValeSaude\LaravelHealthCheck\Config\DatabaseQueueConfig;
use ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface;
use ValeSaude\LaravelHealthCheck\Result;

/**
 * @phpstan-type StuckDatabaseJob object{id: int, queue: string, payload: string, available_at: int}
 */
class DatabaseQueueStuckJobValidator implements ValidatorInterface
{
    /** @var DatabaseQueueConfig */
    private $config;

    public function __construct(DatabaseQueueConfig $config)
    {
        $this->config = $config;
    }

    public function validate(): Result
    {
        $jobs = $this->listAllStuckJobs();
        $threshold = $this->config->getStuckJobThreshold();

        /** @var StuckDatabaseJob $job */
        foreach ($jobs as $job) {
            $stuckTime = time() - $job->available_at;

            if ($stuckTime <= $threshold) {
                continue;
            }

            return Result::unhealthy(
                "The \"{$job->queue}\" queue job {$job->id} is stuck for {$stuckTime}s.",
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
     * @return Collection<StuckDatabaseJob>
     */
    private function listAllStuckJobs(): Collection
    {
        return $this
            ->getConnection()
            ->table($this->config->getTableName())
            ->select(['id', 'queue', 'available_at', 'payload'])
            ->whereNull('reserved_at')
            ->get();
    }
}