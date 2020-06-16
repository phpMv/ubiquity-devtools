<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\scaffolding\ScaffoldCommand;

class CreateCommandCmd extends AbstractCmd {

	public static function run(&$config, $options, $what, $pattern) {
		$what = self::requiredParam($what, 'commandName');
		$value = self::getOption($options, 'v', 'value');
		$description = self::getOption($options, 'd', 'description');
		$parameters = self::getOption($options, 'p', 'parameters');
		$aliases = self::getOption($options, 'a', 'aliases');
		$cmd = new ScaffoldCommand($what, $value, $description, $aliases, $parameters);
		if ($cmd->create($pattern, $cmdPath)) {
			echo ConsoleFormatter::showInfo(sprintf('Command <b>%s</b> created in %s!', $what, $cmdPath), 'Command creation');
		} else {
			echo ConsoleFormatter::showMessage(sprintf('Error during the creation of <b>%s</b>!', $what), 'error', 'Command creation');
		}
	}
}

