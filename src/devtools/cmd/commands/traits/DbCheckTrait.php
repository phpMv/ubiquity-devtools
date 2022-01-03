<?php
namespace Ubiquity\devtools\cmd\commands\traits;

use Ubiquity\cache\CacheManager;
use Ubiquity\controllers\Startup;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\exceptions\DAOException;
use Ubiquity\orm\DAO;

trait DbCheckTrait {

	protected static function checkDbOffset(&$config, $dbOffset) {
		$dbOffsetConfig = DAO::getDbOffset($config, $dbOffset);
		if (! isset($dbOffsetConfig['dbName'])) {
			throw new DAOException("$dbOffset is not configured in app/config/config.php file!");
		}
	}

	protected static function checkModelsCache(&$config) {
		if (\count(CacheManager::modelsCacheUpdated($config))>0) {
			ob_start();
			CacheManager::initCache($config, 'models');
			$res = ob_get_clean();
			echo ConsoleFormatter::showMessage($res, 'success', 'init-cache: models');
		}
	}
}
