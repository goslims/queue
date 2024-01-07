<?php
/**
 * @author Drajat Hasan
 * @email <drajathasan20@gmail.com>
 * @create date 2024-01-07 18:22:53
 * @modify date 2024-01-07 18:26:48
 * @license GPLv3
 * @desc [description]
 */
namespace SLiMS\Queue\Handlers;

use Closure;

abstract class Standart
{
    /**
     * Handler name
     */
    protected string $name = '';

    /**
     * Handler options
     */
    protected array $options = [];

    /**
     * Default topic
     */
    protected string $topic = '';

    public function __construct()
    {
        $this->topic = config('queue.default_topic', 'slims');
    }

    /**
     * An abstract method
     * about producing message/data
     * to handler
     *
     * @param string $message
     * @param string $topic
     * @return void
     */
    abstract protected function produce(string $message, string $topic = ''):void;

    /**
     * AN abstract method
     * about consuming queue data
     *
     * @param string $topic
     * @param Closure $callback
     * @return void
     */
    abstract protected function consume(string $topic, Closure $callback):void;

    /**
     * Setter for option
     *
     * @param array $options
     * @return void
     */
    public function setOptions(array $options):void
    {
        $this->options = $options;
    }

    /**
     * Getter for handler options
     *
     * @return void
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Getter for some option
     *
     * @param string $key
     * @return void
     */
    public function getOption(string $key)
    {
        return $this->options[$key]??null;
    }

    public function getName()
    {
        return empty($this->name) ? get_called_class() : $this->name;
    }
}