<?php
error_reporting ( E_ALL );
if (! defined ( 'DS' )) {
	define ( 'DS', DIRECTORY_SEPARATOR );
	define ( 'ROOT', __DIR__ . \DS .'..'.\DS. 'app' . \DS );
}
$config = include ROOT . 'config/config.php';
$config ["siteUrl"] = '%siteURL%';
$config ['sessionName'] = '%sessionName%';
require ROOT . './../vendor/autoload.php';
require ROOT . 'config/services.php';
\Ubiquity\controllers\Startup::run ( $config );
