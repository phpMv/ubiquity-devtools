<?php
use Ubiquity\controllers\Router;

\Ubiquity\cache\CacheManager::startProd($config);
\Ubiquity\orm\DAO::startDatabase($config);
Router::start();
Router::addRoute("_default", "controllers\\IndexController");
