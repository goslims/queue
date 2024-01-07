<?php
namespace Test\Commands;

use SLiMS\Cli\Command;
use SLiMS\Queue\Manager as Queue;

class QueueConsumer extends Command
{
    /**
     * Signature is combination of command name
     * argument and options
     *
     * @var string
     */
    protected string $signature = 'queue:consume';

    /**
     * Command description
     *
     * @var string
     */
    protected string $description = 'Consume a job';

    /**
     * Handle command process
     *
     * @return void
     */
    public function handle()
    {
        // Basic usage
        Queue::consume(topic: 'slims', callback: function($data, $jobSeq) {
            $data = json_decode($data);
            dump($data);
            $this->info('Finish job ' . $jobSeq);
        });

        // With channel
        // Queue::setChannel('youtube')->consume(topic: 'slims', callback: function($data, $jobSeq) {
        //     $data = json_decode($data);
        //     dump($data);
        //     $this->info('Finish job ' . $jobSeq);
        // });
    }
} 