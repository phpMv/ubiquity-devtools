<?php
namespace Ubiquity\devtools\utils;

use Ubiquity\cache\CacheManager;
use Ubiquity\controllers\admin\popo\Route;
use Ubiquity\controllers\Router;
use Ubiquity\orm\OrmUtils;
use Ubiquity\contents\validation\ValidatorsManager;

class FrameworkParts {

	public static function getRoutes($config, $type = 'all') {
		self::initCache($config, $type);
		$routes = Router::getRoutes();
		return Route::init($routes);
	}

	protected static function initCache($config, $type = 'all') {
		CacheManager::startProd($config);
		switch ($type) {
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

	public static function testRoutes($config, $search, $method = null, $type = 'all') {
		self::initCache($config, $type);
		$routes = Router::testRoutes($search, $method);
		return Route::init($routes);
	}

	public static function getModelInfos($model, $config) {
		CacheManager::startProd($config);
		return OrmUtils::getModelMetadata($model);
	}

	public static function getValidatorsInfo($model, $config) {
		CacheManager::startProd($config);
		return ValidatorsManager::getCacheInfo($model);
	}

	public static function getMailerQueue($config) {
		CacheManager::startProd($config);
		return \Ubiquity\controllers\admin\popo\MailerQueuedClass::initQueue();
	}

	public static function getMailerDeQueue($config) {
		CacheManager::startProd($config);
		return \Ubiquity\controllers\admin\popo\MailerQueuedClass::initQueue(true);
	}

	public static function getMailerClasses($config) {
		CacheManager::startProd($config);
		return \Ubiquity\controllers\admin\popo\MailerClass::init();
	}
}

