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
        try {
            Queue::consume('slims', function($jobSeq, $data) {
                $data = json_decode($data);
                dump($data);
                $this->info('Finish job ' . $jobSeq);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
} 