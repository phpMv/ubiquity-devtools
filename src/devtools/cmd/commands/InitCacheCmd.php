<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\cache\CacheManager;

/**
 * Initialize cache.
 * Ubiquity\devtools\cmd\commands$InitCacheCmd
 *
 * @author jc
 * @version 1.0.0
 *
 */
class InitCacheCmd extends AbstractCmd {

	public static function run(&$config, $options) {
		$type = self::getOption($options, "t", "type", "all");
		ob_start();
		CacheManager::initCache($config, $type);
		$res = ob_get_clean();
		echo ConsoleFormatter::showMessage($res, 'success', 'init-cache:' . $type);
	}

}

