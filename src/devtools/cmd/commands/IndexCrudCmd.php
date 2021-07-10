<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\cache\CacheManager;

class IndexCrudCmd extends AbstractCmdModel {

	public static function run(&$config, $options, $what) {
		CacheManager::start($config);
		$scaffold = new ConsoleScaffoldController();
		$crudDatas = self::getOption($options, 'd', 'datas', true);
		$crudViewer = self::getOption($options, 'v', 'viewer', true);
		$crudEvents = self::getOption($options, 'e', 'events', true);
		$crudViews = self::getOption($options, 't', 'templates', 'index,form,display,item,itemHome');
		$routePath = self::getOption($options, 'p', 'path', '{resource}');
		if (strpos($routePath, '{resource') === false) {
			echo ConsoleFormatter::showMessage("The path variable <b>{$routePath}</b> does not contain the {resource} part!", 'error', 'index-crud-controller');
		} else {
			$scaffold->addIndexCrudController($what, $crudDatas, $crudViewer, $crudEvents, $crudViews, $routePath);
		}
	}
}

