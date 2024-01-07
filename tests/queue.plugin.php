<?php
/**
 * Plugin Name: Queue Test
 * Plugin URI: -
 * Description: -
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://t.me/drajathasan
 */

use SLiMS\Plugins;

require __DIR__ . '/../vendor/autoload.php';

Plugins::getInstance()->registerCommand(new \Test\Commands\QueueConsumer);
Plugins::getInstance()->registerCommand(new \Test\Commands\QueueProducer);