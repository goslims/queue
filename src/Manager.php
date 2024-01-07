<?php
/**
 * @author Drajat Hasan
 * @email <drajathasan20@gmail.com>
 * @create date 2024-01-07 18:14:32
 * @modify date 2024-01-07 18:22:37
 * @license GPLv3
 * @desc a library to manage queue process 
 * with sql and non sql database
 */

namespace SLiMS\Queue;

use Exception;
use SLiMS\Config;
use SLiMS\Queue\Handlers\Standart;

final class Manager
{
    private static ?Manager $instance = null;
    private ?Config $config = null;
    private ?object $handler_instance = null;
    private string $handler_class = '';
    private array $handler_option = [];

    private function __construct()
    {
        $this->config = Config::getInstance();
        $this->createBaseConfig();
        $this->loadConfig();
        $this->handlerInit();
    }

    /**
     * Register new config if not exists
     *
     * @return void
     */
    private function createBaseConfig()
    {
        if ($this->config->get('queue') === null) {
            Config::create('queue', file_get_contents(__DIR__ . '/../config/queue.php'));
        }
    }

    /**
     * Load queue options
     * from config file
     *
     * @return void
     */
    private function loadConfig()
    {
        // loading queue config, but if it not exist
        // just retrive from local config
        $config = $this->config->get('queue', default: require __DIR__ . '/../config/queue.php');

        // default handler configuration
        $handler = $config['handlers'][$config['default_handler']];

        $this->handler_class = $handler['class'];

        /**
         * register options as object property
         */
        foreach ($handler['options'] as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Validate and initialization
     * handler instance
     *
     * @return void
     */
    private function handlerInit()
    {
        $this->handler_instance = new $this->handler_class;

        if (!$this->handler_instance instanceof Standart) throw new Exception("Class {$this->handler_class} is not using handler standart");

        $this->handler_instance->setOptions($this->handler_option);
    }

    public static function getInstance()
    {
        if (self::$instance === null) self::$instance = new Manager;
        return self::$instance;
    }

    /**
     * Overriding current handler if needed
     *
     * @param string $handlerName
     * @return void
     */
    public static function use(string $handlerName)
    {
        $instance = self::getInstance();
        $config = $instance->config->get('queue');
        $handler = $config['handlers'][$handlerName];

        $instance->handler_class = $handler['class'];
        $instance->handler_option = $handler['options'];
        $instance->handlerInit();

    }

    /**
     * Define magic method
     * to organize objeect property
     *
     * @param string $key
     * @return boolean
     */
    public function __isset(string $key)
    {
        return isset($this->handler_option[$key]);
    }

    public function __unset(string $key)
    {
        unset($this->handler_option[$key]);
    }

    public function __set(string $key, $value) {
        $this->handler_option[$key] = $value;
    }

    public function __get(string $key) {
        return $this->handler_option[$key]??null;
    }

    public static function __callStatic(string $method, array $arguments)
    {
        $instance = self::getInstance();
        if (method_exists($instance->handler_instance, $method)) {
            return call_user_func_array([$instance->handler_instance, $method], $arguments);
        }

        throw new Exception("Uknown function {$method} in handler instance");
    }
}