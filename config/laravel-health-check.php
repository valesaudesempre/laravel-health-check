<?php

use ValeSaude\LaravelHealthCheck\Config\DatabaseQueueConfig;

return [
    'settings' => [
        DatabaseQueueConfig::class => [
            'connection_name' => 'default',
            'table_name' => 'queues',
            'max_size' => 50,
            'global_max_execution_time' => 60,
        ],
    ],
];