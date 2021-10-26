<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\cache\CacheManager;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\domains\DDDManager;

class IndexCrudCmd extends AbstractCmdModel {

	public static function run(&$config, $options, $what) {
		CacheManager::start($config);
		$scaffold = new ConsoleScaffoldController();
		$crudDatas = self::getOption($options, 'd', 'datas', true);
		$crudViewer = self::getOption($options, 'v', 'viewer', true);
		$crudEvents = self::getOption($options, 'e', 'events', true);
		$crudViews = self::getOption($options, 't', 'templates', 'index,form,display,home,itemHome');
		$routePath = self::getOption($options, 'p', 'path', '{resource}');
		$dbOffset = self::getOption($options, 'a', 'database', '');
		$domain = self::updateDomain($options);
		$dbs = DDDManager::getDatabases();
		if ($dbOffset == '' || \array_search($dbOffset, $dbs) === false) {
			if (\count($dbs) > 1) {
				$dbOffset = Console::question('Please select a valid database offset', $dbs);
			} else {
				$dbOffset = \current($dbs);
			}
			$scaffold->setActiveDb($dbOffset);
		}
		if (\strpos($routePath, '{resource') === false) {
			echo ConsoleFormatter::showMessage("The path variable <b>{$routePath}</b> does not contain the {resource} part!", 'error', 'index-crud-controller');
		} else {
			$scaffold->addIndexCrudController($what, $crudDatas, $crudViewer, $crudEvents, $crudViews, $routePath);
		}
	}
}

