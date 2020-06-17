<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\Command;
use Ubiquity\devtools\cmd\ConsoleFormatter;

/**
 * Give some infos about a command.
 * Ubiquity\devtools\cmd\commands$HelpCmd
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class HelpCmd extends AbstractCmd {

	public static function run($caller, $what = null) {
		if (isset($what)) {
			self::infoCmd($what);
		} else {
			self::info($caller);
		}
	}

	private static function infoCmd($cmd) {
		$infos = Command::getInfo($cmd);
		$command = null;
		foreach ($infos as $info) {
			echo ConsoleFormatter::showInfo($info['info']);
			if ($command !== $info['cmd']) {
				echo ConsoleFormatter::formatHtml($info['cmd']->longString());
			}
			$command = $info['cmd'];
			echo "\n";
		}
	}

	private static function info($caller) {
		echo $caller::getAppVersion() . "\n";
		$commands = Command::getCommands();
		foreach ($commands as $command) {
			echo ConsoleFormatter::formatHtml($command->longString());
			echo "\n";
		}
	}
}

