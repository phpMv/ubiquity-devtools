<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\cache\CacheManager;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\orm\reverse\DatabaseReversor;
use Ubiquity\db\reverse\DbGenerator;
use Ubiquity\devtools\cmd\ConsoleTable;

class MigrationsCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		$domain = self::updateDomain($options);
		$dbOffset = self::getOption($options, 'd', 'database', 'default');

		$domainStr = '';
		if ($domain != '') {
			$domainStr = " in the domain <b>$domain</b>";
		}

		CacheManager::start($config);
		$generator = new DatabaseReversor(new DbGenerator(), $activeDb);
		$generator->migrate();
		$script = $generator->getScript();

		if (\count($script) === 0) {
			echo ConsoleFormatter::showMessage("No migrations to operate for db at offset <b>$dbOffset</b>$domainStr!", 'info', 'Migrations');
		} else {
			echo ConsoleFormatter::showMessage("Migrations to operate for db at offset <b>$dbOffset</b>$domainStr:", 'info', 'Migrations');
			self::displayScript(self::scriptToLineArray($script));
		}
	}

	private static function displayScript(array $script) {
		$tbl = new ConsoleTable();
		$tbl->setIndent(5);
		$tbl->setDatas($script);
		echo $tbl->getTable();
	}

	private static function scriptToLineArray(array $script): array {
		$result = [];
		$line = 1;
		foreach ($script as $line) {
			$result = [
				'line' => $line ++,
				'sql' => $line
			];
		}
		return $result;
	}
}

