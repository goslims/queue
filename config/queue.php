<?php

return [
    'default_handler' => 'database',
    'default_topic' => 'slims',
    'handlers' => [
        'database' => [
            'class' => \SLiMS\Queue\Handlers\Database::class,
            'options' => [
                'table' => 'queue',
                'order' => 'asc',
                'sort_by' => 'created_at',
                'delay_per_job' => 5 // in second
            ]
        ]
    ]
];