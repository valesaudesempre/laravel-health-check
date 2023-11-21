<?php

namespace ValeSaude\LaravelHealthCheck\Config;

/**
 * @codeCoverageIgnore
 */
class DatabaseQueueConfig extends AbstractValidatorConfig
{
    public function getConnectionName(): string
    {
        return $this->getSettings('connection_name', 'default');
    }

    public function getTableName(): string
    {
        return $this->getSettings('table_name', 'queues');
    }

    public function getMaxSize(): int
    {
        return $this->getSettings('max_size', 50);
    }

    public function getMaxExecutionTimeForQueue(string $queue): int
    {
        $queueSpecificMaxExecutionTime = $this->getSettings("max_execution_time.{$queue}");

        return $queueSpecificMaxExecutionTime ?? $this->getSettings('global_max_execution_time', 60);
    }

    public function getStuckJobThreshold(): int
    {
        return $this->getSettings('stuck_job_threshold', 10);
    }
}