<?php
namespace Test\Commands;

use SLiMS\Cli\Command;
use SLiMS\Queue\Manager as Queue;

class QueueProducer extends Command
{
    /**
     * Signature is combination of command name
     * argument and options
     *
     * @var string
     */
    protected string $signature = 'queue:produce {message}';

    /**
     * Command description
     *
     * @var string
     */
    protected string $description = 'Produce a task';

    /**
     * Handle command process
     *
     * @return void
     */
    public function handle()
    {
        Queue::produce(json_encode($this->argument('message')));
    }
} 