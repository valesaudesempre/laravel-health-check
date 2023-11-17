<?php

use ValeSaude\LaravelHealthCheck\Validators\DatabaseQueueSizeValidator;

return [
    'settings' => [
        DatabaseQueueSizeValidator::class => [
            'max_size' => 50,
            'connection_name' => 'default',
            'table_name' => 'queues',
        ],
    ],
];