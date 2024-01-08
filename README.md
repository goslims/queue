# SLiMS Queue
A SLiMS component to manage queue processes. You can provide your handler, please read handler standart in ```src/Handlers/```.
## How to
### Produce
```php
use SLiMS\Queue\Manager as Queue;

Queue::produce('your message/formatter task etc'); // default topic is slims

// or 

Queue::produce('your message/formatter task etc', topic: 'csv_process');
```

### Consume
```php
use SLiMS\Queue\Manager as Queue;

Queue::consume(topic: 'slims', callback: function($message){
    // write your code here
});

// with channel name

Queue::setChannel('worker1')->consume(topic: 'slims', callback: function($message){
    // write your code here
});
```

### Config
Queue config available in ```config/queue.php```
```php
<?php

return [
    'default_handler' => 'database', // change it with yours. e.g : nsq
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
        ],
        /*'nsq' => [
            'class' => your_handler::class,
            'options' => [
                // your options here, such as host etc
            ]
        ]*/
    ]
];
```
