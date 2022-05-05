#!/usr/bin/env php
<?php
// workerman.php

if (! defined ( 'DS' )) {
	define ( 'DS', DIRECTORY_SEPARATOR );
	define ( 'ROOT', __DIR__ . \DS .'..'.\DS. 'app' . \DS );
}
$config=include ROOT.'config/config.php';
$sConfig= include __DIR__.\DS.'workerman-config.php';
$config["sessionName"]=$sConfig["sessionName"];
$address=$sConfig['host'].':'.$sConfig['port'];
$config ["siteUrl"] = 'http://'.$address;
require ROOT . './../vendor/autoload.php';
$workerServer=new \Ubiquity\servers\workerman\WorkermanServer();
$workerServer->init($config, realpath(__DIR__.\DS.'..'.\DS.'public'.\DS));
$workerServer->setDefaultCount();
require ROOT.'config/services.php';
$workerServer->run($sConfig['host'],$sConfig['port'],$sConfig['socket']??[]);
