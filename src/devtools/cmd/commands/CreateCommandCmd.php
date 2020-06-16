<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\scaffolding\ScaffoldCommand;
use Ubiquity\devtools\cmd\Command;

class CreateCommandCmd extends AbstractCmd {

	public static function run(&$config, $options, $what, $devtoolsConfig, $caller) {
		$pattern = $devtoolsConfig['cmd-pattern'] ?? 'commands' . \DS . '*.cmd.php';
		$what = self::requiredParam($what, 'commandName');
		$value = self::getOption($options, 'v', 'value');
		$description = self::getOption($options, 'd', 'description');
		$parameters = self::getOption($options, 'p', 'parameters');
		$aliases = self::getOption($options, 'a', 'aliases');
		$cmd = new ScaffoldCommand($what, $value, $description, $aliases, $parameters);
		if ($cmd->create($pattern, $cmdPath)) {
			echo ConsoleFormatter::showMessage(sprintf('Command <b>%s</b> created in %s!', $what, $cmdPath), 'success', 'Command creation');
			Command::reloadCustomCommands($devtoolsConfig);
			HelpCmd::run($caller, $what);
		} else {
			echo ConsoleFormatter::showMessage(sprintf('Error during the creation of <b>%s</b>!', $what), 'error', 'Command creation');
		}
	}
}

