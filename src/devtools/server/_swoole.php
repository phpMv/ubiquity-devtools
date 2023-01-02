#!/usr/bin/env php
<?php
// swoole.php

if (! defined ( 'DS' )) {
	define ( 'DS', DIRECTORY_SEPARATOR );
	define ( 'ROOT', __DIR__ . \DS .'..'.\DS. 'app' . \DS );
}
$config=include ROOT.'cache/config/config.cache.php';
$sConfig= include __DIR__.\DS.'swoole-config.php';
$config["sessionName"]=$sConfig["sessionName"];
$address=$sConfig['host'].':'.$sConfig['port'];
$config ["siteUrl"] = 'http://'.$address;
require ROOT . './../vendor/autoload.php';
$swooleServer=new \Ubiquity\servers\swoole\SwooleServer();
$swooleServer->init($config, realpath(__DIR__.\DS.'..'.\DS.'public'.\DS));
require ROOT.'config/services.php';
$swooleServer->run($sConfig['host'],$sConfig['port'],$sConfig['options']??[]);
