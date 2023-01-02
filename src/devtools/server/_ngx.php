<?php
// ngx.php
if (! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', __DIR__ . \DS . '..' . \DS . 'app' . \DS);
}
$config = include ROOT . 'cache/config/config.cache.php';
$sConfig = include __DIR__ . \DS . 'ngx-config.php';
$config["sessionName"] = $sConfig["sessionName"];
$address = $sConfig['host'] . ':' . $sConfig['port'];
$config["siteUrl"] = 'http://' . $address;
require ROOT . './../vendor/autoload.php';
\Ubiquity\servers\ngx\NgxServer::init($config);
require ROOT . 'config/services.php';

function handle() {
	\Ubiquity\servers\ngx\NgxServer::handleRequest();
}