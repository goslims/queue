<?php
/**
 * @author Drajat Hasan
 * @email <drajathasan20@gmail.com>
 * @create date 2024-01-07 18:26:54
 * @modify date 2024-01-08 06:03:01
 * @license GPLv3
 * @desc [description]
 */

namespace SLiMS\Queue\Handlers;

use Closure;
use SLiMS\DB;
use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class Database extends Standart
{
    private bool $break = false;
    
    public function __construct()
    {
        parent::__construct();
        $this->migrate();
    }

    /**
     * Store data to database
     *
     * @param string $message
     * @param string $topic
     * @return void
     */
    public function produce(string $message, string $topic = ''):void
    {
        if (empty($topic)) $topic = $this->topic;
        $db = DB::getInstance();
        
        $insert = $db->prepare('insert into `' . $this->getOption('table') . '` set `message` = ?, `topic` = ?, `created_at` = now()');
        $insert->execute([
            $message,
            $topic
        ]);
    }

    /**
     * Consume a queue data from database
     *
     * @param string $topic
     * @param Closure $callback
     * @return void
     */
    public function consume(string $topic, Closure $callback):void
    {
        $db = DB::getInstance();
        if (empty($this->channel)) $this->channel = substr(md5(rand(1,100)), 0,15);

        while (true) {
            $db->exec('START TRANSACTION');
            $queue = $db->prepare('
                select `id`, `message` from `' . $this->getOption('table') . '`' .
                ' where `topic` = ? and `status` = 0 order by ' . $this->getOption('sort_by') . ' ' . $this->getOption('order') .
                ' limit 1 FOR UPDATE SKIP LOCKED');
            $queue->execute([$topic]);
            $data = $queue->fetch(\PDO::FETCH_ASSOC);

            if ($queue->rowCount() < 1) {
                $db->exec('COMMIT');
                continue;
            }

            $callback($data['message'], $data['id']);

            $update = $db->prepare('update `' . $this->getOption('table') . '` set `status` = 1, `channel` = ?,`updated_at` = now() where `id` = ?');
            $update->execute([$this->channel, $data['id']]);
            
            $db->exec('COMMIT');

            unset($queue);
            unset($update);
            unset($data);

            sleep($this->getOption('delay_per_job'));

            if ($this->break) break;
        }
    }

    /**
     * Stop iteration process
     *
     * @return void
     */
    public function terminate()
    {
        $this->break = true;
    }

    /**
     * Create queue table
     *
     * @return void
     */
    private function migrate()
    {
        Schema::create('queue', function(Blueprint $table) {
            $table->autoIncrement('id');
            $table->string('topic', 30)->notNull();
            $table->string('channel', 30)->nullable();
            $table->text('message')->notNull();
            $table->tinynumber('status', 1)->default(0);
            $table->timestamps();
            $table->index('topic');
            $table->index('channel');
            $table->fulltext('message');
            $table->engine = 'InnoDB';
        });
    }
}