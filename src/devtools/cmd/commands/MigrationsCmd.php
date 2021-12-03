<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\cache\CacheManager;
use Ubiquity\devtools\cmd\commands\traits\DbCheckTrait;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\utils\arrays\ClassicArray;
use Ubiquity\devtools\utils\arrays\ReflectArray;
use Ubiquity\exceptions\DAOException;
use Ubiquity\orm\DAO;
use Ubiquity\orm\reverse\DatabaseReversor;
use Ubiquity\db\reverse\DbGenerator;
use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\cmd\Screen;

class MigrationsCmd extends AbstractCmd {

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

		$generator = new DatabaseReversor(new DbGenerator(), $dbOffset);
		$generator->migrate();
		$script = $generator->getScript();
		if (\count($script) === 0) {
			echo ConsoleFormatter::showMessage("No migrations to operate for db at offset <b>$dbOffset</b>$domainStr!", 'info', 'Migrations');
		} else {
			echo ConsoleFormatter::showMessage("Migrations to operate for db at offset <b>$dbOffset</b>$domainStr:", 'info', 'Migrations');
			do {
				self::displayScript(self::scriptToLineArray($script));
				$rep = Console::question('Select your choices:', [
					'Execute all commands',
					'Delete a row',
					'Quit'
				]);
				switch ($rep) {
					case 'Delete a row':
						$count = \count($script);
						$row = Console::question("Enter a valid row between 1 and $count:");
						$delete = self::deleteScriptRow($row, $script);
						if ($delete !== false) {
							$script = $delete;
						}
						break;
					case 'Execute all commands':
						self::executeSQLTransaction($dbOffset, implode(';', $script), 'Database migrations');
						$rep = 'Quit';
						break;
					default:
						echo ConsoleFormatter::showInfo('Operation terminated, Bye!');
				}
			} while ($rep !== 'Quit');
		}
	}

	private static function deleteScriptRow(int $rowNum, array $script) {
		$rowNum --;
		if ($rowNum >= 0 && $rowNum < \count($script)) {
			unset($script[$rowNum]);
			return array_values($script);
		}
		return false;
	}

	private static function displayScript(array $script) {
		$tbl = new ConsoleTable();
		$tbl->setIndent(5);
		$tbl->setPadding(1);
		$tbl->setDatas($script);
		echo $tbl->getTable();
	}

	private static function scriptToLineArray(array $script): array {
		$result = [];
		$line = 1;
		$width = Screen::getWidth() - 20;
		foreach ($script as $sql) {
			$result[] = [
				'line' => $line ++,
				'sql' => \wordwrap($sql, $width, PHP_EOL)
			];
		}
		return $result;
	}

	private static function executeSQLTransaction(string $activeDbOffset, string $sql, string $title) {
		$isValid = true;
		if (isset($sql)) {
			$db = DAO::getDatabase($activeDbOffset ?? 'default');
			if (! $db->isConnected()) {
				$db->setDbName('');
				try {
					$db->connect();
				} catch (\Exception $e) {
					$isValid = false;
					echo ConsoleFormatter::showMessage($e->getMessage(), 'error', $title);
				}
			}
			if ($isValid) {
				if ($db->beginTransaction()) {
					try {
						$db->execute($sql);
						if ($db->inTransaction()) {
							$db->commit();
						}
						echo ConsoleFormatter::showMessage("Database created/updated with success at offset $activeDbOffset!", 'success', $title);
					} catch (\Error $e) {
						if ($db->inTransaction()) {
							$db->rollBack();
						}
						echo ConsoleFormatter::showMessage($e->getMessage(), 'error', $title);
					}
				} else {
					$db->execute($sql);
					echo ConsoleFormatter::showMessage("Database created/updated with success at offset $activeDbOffset!", 'success', $title);
				}
			}
		}
	}
}

