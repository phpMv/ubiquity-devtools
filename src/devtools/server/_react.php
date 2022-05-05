#!/usr/bin/env php
<?php
// react.php

if (! defined ( 'DS' )) {
	define ( 'DS', DIRECTORY_SEPARATOR );
	define ( 'ROOT', __DIR__ . \DS .'..'.\DS. 'app' . \DS );
}
$config=include ROOT.'config/config.php';
$sConfig= include __DIR__.\DS.'react-config.php';
$config["sessionName"]=$sConfig["sessionName"];
$address=$sConfig['host'].':'.$sConfig['port'];
$config ["siteUrl"] = 'http://'.$address.'/';
require ROOT . './../vendor/autoload.php';
$reactServer=new \Ubiquity\servers\react\ReactServer();
$reactServer->init($config, realpath(__DIR__.\DS.'..'.\DS.'public'.\DS));
require ROOT.'config/services.php';
$reactServer->run($address);
