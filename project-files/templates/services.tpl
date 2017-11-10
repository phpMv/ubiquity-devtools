<?php
use micro\cache\CacheManager;
use micro\controllers\Router;
use micro\orm\DAO;

/*if($config["test"]){
 \micro\log\Logger::init();
 $config["siteUrl"]="http://127.0.0.1:8090/";
 }*/

CacheManager::startProd($config);
$db=$config["database"];
if($db["dbName"]!==""){
	DAO::connect($db["type"],$db["dbName"],@$db["serverName"],@$db["port"],@$db["user"],@$db["password"],@$db["cache"]);
}
Router::start();
Router::addRoute("_default", "controllers\Main");