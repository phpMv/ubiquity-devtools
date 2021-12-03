<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\cache\CacheManager;
use Ubiquity\devtools\cmd\commands\traits\DbCheckTrait;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\orm\reverse\DatabaseChecker;

class InfoMigrationsCmd extends AbstractCmd {

	use DbCheckTrait;

	public static function run(&$config, $options, $what) {
		$domain = self::updateDomain($options);
		CacheManager::start($config);
		self::checkModelsCache($config);
		$dbOffset = self::getOption($options, 'd', 'database', 'default');
		self::checkDbOffset($config, $dbOffset);
		$domainStr = '';
		if ($domain != '') {
			$domainStr = " in the domain <b>$domain</b>";
		}

		$checker = new DatabaseChecker($dbOffset);
		$checker->checkAll();

		if ($checker->hasErrors()) {
			echo ConsoleFormatter::showMessage("Migrations to operate for db at offset <b>$dbOffset</b>$domainStr:", 'info', 'Migrations');
			$messages = [];
			$checker->displayAll(function ($type, $icons, $content) use (&$messages) {
				$messages[$icons][] = $content;
			});
			foreach ($messages as $title => $msgs) {
				$content = \implode(PHP_EOL, $msgs);
				echo ConsoleFormatter::showMessage($content, 'warning', $title);
			}
		} else {
			echo ConsoleFormatter::showMessage("No migrations to operate for db at offset <b>$dbOffset</b>$domainStr!", 'info', 'Migrations');
		}
	}
}

