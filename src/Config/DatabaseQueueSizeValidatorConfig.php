<?php

namespace ValeSaude\LaravelHealthCheck\Config;

/**
 * @codeCoverageIgnore
 */
class DatabaseQueueSizeValidatorConfig extends AbstractValidatorConfig
{
    public function getMaxSize(): int
    {
        return $this->getSettings('max_size', 50);
    }

    public function getConnectionName(): string
    {
        return $this->getSettings('connection_name', 'default');
    }

    public function getTableName(): string
    {
        return $this->getSettings('table_name', 'queues');
    }
}