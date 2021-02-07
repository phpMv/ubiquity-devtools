<?php
error_reporting(E_ALL);
if (! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', __DIR__ . \DS . '..' . \DS . 'app' . \DS);
}
$config = include ROOT . 'config/config.php';
$sConfig = include __DIR__ . \DS . 'config.php';
$config["siteUrl"] = 'http://' . $sConfig['host'] . ':' . $sConfig['port'] . '/';
$config['sessionName'] = $sConfig['sessionName'];
require ROOT . './../vendor/autoload.php';
$config['debug'] = true;
if (class_exists("\\Monolog\\Logger")) {
	$config['logger'] = function () use ($sConfig) {
		return new \Ubiquity\log\libraries\UMonolog($sConfig['sessionName'], \Monolog\Logger::INFO);
	};
	\Ubiquity\log\Logger::init($config);
}

require ROOT . 'config/services.php';

if (\Ubiquity\debug\Debug::hasLiveReload()) {
	echo \Ubiquity\debug\Debug::liveReload();
}
\Ubiquity\controllers\Startup::run($config);
