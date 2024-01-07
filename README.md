# SLiMS Queue
A library component to SLiMS, manage queue task for asyncronous process with many database, by default is MariaDB or MySQL. You can provide your handler 😉.
## How to
### Produce
```php
use SLiMS\Queue\Manager as Queue;

Queue::produce('your message/formatter task etc'); // by default topic is slims

// or 

Queue::produce('your message/formatter task etc', topic: 'csv_process');
```

### Consume
```php
use SLiMS\Queue\Manager as Queue;

Queue::consume(topic: 'slims', callback: function($message){
    // write your code here
});
```