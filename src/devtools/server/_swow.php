#!/usr/bin/env php
<?php
// swow.php

if (! defined ( 'DS' )) {
	define ( 'DS', DIRECTORY_SEPARATOR );
	define ( 'ROOT', __DIR__ . \DS .'..'.\DS. 'app' . \DS );
}
$config=include ROOT.'cache/config/config.cache.php';
$sConfig= include __DIR__.\DS.'swow-config.php';
$config["sessionName"]=$sConfig["sessionName"];
$address=$sConfig['host'].':'.$sConfig['port'];
$config ["siteUrl"] = 'http://'.$address;
require ROOT . './../vendor/autoload.php';
$swowServer=new \Ubiquity\servers\swow\SwowServer();
$swowServer->init($config, realpath(__DIR__.\DS.'..'.\DS.'public'.\DS));
require ROOT.'config/services.php';
$swowServer->run($sConfig['host'],$sConfig['port'],$sConfig['socket']??[]);
