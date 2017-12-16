<?php
use Ubiquity\cache\CacheManager;
use Ubiquity\controllers\Router;
use Ubiquity\orm\DAO;

/*if($config["test"]){
 \Ubiquity\log\Logger::init();
 $config["siteUrl"]="http://127.0.0.1:8090/";
 }*/

CacheManager::startProd($config);
$db=$config["database"];
if($db["dbName"]!==""){
	DAO::connect($db["type"],$db["dbName"],@$db["serverName"],@$db["port"],@$db["user"],@$db["password"],@$db["options"],@$db["cache"]);
}
Router::start();
Router::addRoute("_default", "controllers\Main");