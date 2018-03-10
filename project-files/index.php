<?php
use \Ubiquity\controllers\Startup;
error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath('app').DS);

$config=include_once ROOT.'config/config.php';

require_once ROOT.'./../vendor/autoload.php';

require_once ROOT.'config/services.php';
Startup::run($config,$_GET["c"]);