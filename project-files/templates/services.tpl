<?php
use Ubiquity\controllers\Router;

/*if($config["test"]){
\Ubiquity\log\Logger::init($config);
 $config["siteUrl"]="http://127.0.0.1:8090/";
}*/

\Ubiquity\cache\CacheManager::startProd($config);
\Ubiquity\orm\DAO::startDatabase($config);
Router::start();
Router::addRoute("_default", "controllers\\IndexController");
