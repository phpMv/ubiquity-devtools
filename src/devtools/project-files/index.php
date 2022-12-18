<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS . '..' . DS . 'app' . DS);
$config = include ROOT . 'cache/config/config.cache.php';
require ROOT . './../vendor/autoload.php';
require ROOT . 'config/services.php';
\Ubiquity\controllers\Startup::run($config);
