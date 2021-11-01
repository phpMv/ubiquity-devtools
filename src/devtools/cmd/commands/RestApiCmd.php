<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\cache\CacheManager;

class RestApiCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		CacheManager::start($config);
		self::updateDomain($options);
		$scaffold = new ConsoleScaffoldController();
		$routePath = self::getOption($options, 'p', 'path', '');
		$api = self::getOption($options, 'a', 'api', 'json');
		if ($api === 'json') {
			$scaffold->addJsonRestController($what, $routePath);
		} else {
			$scaffold->addRestApiController($what, $routePath);
		}
	}
}

