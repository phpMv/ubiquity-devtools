<?php
namespace devtools\utils;
use Ubiquity\cache\CacheManager;
use Ubiquity\controllers\admin\popo\Route;
use Ubiquity\controllers\Router;

class FrameworkParts {

	public static function getRoutes($config,$type='all'){
		self::initCache($config,$type);
		$routes=Router::getRoutes();
		return Route::init ( $routes );
	}

	protected static function initCache($config,$type='all'){
		CacheManager::startProd($config);
		switch ($type){
			case "routes":
				Router::start();
				break;
			case "rest":
				Router::startRest();
				break;
			default:
				Router::startAll();
		}
	}

	public static function testRoutes($config,$search,$method=null,$type='all'){
		self::initCache($config,$type);
		$routes=Router::testRoutes($search,$method);
		return Route::init($routes);
	}
}

