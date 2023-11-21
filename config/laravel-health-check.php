<?php

use ValeSaude\LaravelHealthCheck\Config\DatabaseQueueConfig;
use ValeSaude\LaravelHealthCheck\Validators\DatabaseQueueJobExecutionTimeValidator;
use ValeSaude\LaravelHealthCheck\Validators\DatabaseQueueSizeValidator;
use ValeSaude\LaravelHealthCheck\Validators\DatabaseQueueStuckJobValidator;

return [
    'settings' => [
        DatabaseQueueConfig::class => [
            'connection_name' => 'default',
            'table_name' => 'jobs',
            'max_size' => 50,
            'global_max_execution_time' => 60,
            'stuck_job_threshold' => 10,
        ],
    ],
    'profiles' => [
        'queue' => [
            DatabaseQueueSizeValidator::class,
            DatabaseQueueJobExecutionTimeValidator::class,
            DatabaseQueueStuckJobValidator::class,
        ],
    ],
];