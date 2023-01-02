<?php
if (! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', __DIR__ . \DS . '..' . \DS . 'app' . \DS);
}
$config = include ROOT . 'cache/config/config.cache.php';
$sConfig = include __DIR__ . \DS . 'config.php';
$config["siteUrl"] = 'http://' . $sConfig['host'] . ':' . $sConfig['port'] . '/';
$config['sessionName'] = $sConfig['sessionName'];
require ROOT . './../vendor/autoload.php';
$config['debug'] = true;
if (\class_exists("\\Monolog\\Logger")) {
	$config['logger'] = function () use ($sConfig) {
		return new \Ubiquity\log\libraries\UMonolog($sConfig['sessionName'], \Monolog\Logger::INFO);
	};
	\Ubiquity\log\Logger::init($config);
}

\Ubiquity\debug\Debugger::start($config);

require ROOT . 'config/services.php';

\Ubiquity\assets\AssetsManager::setAssetsFolder();

\Ubiquity\controllers\Startup::run($config);

if (\Ubiquity\debug\LiveReload::hasLiveReload()) {
	echo \Ubiquity\debug\LiveReload::start();
}
