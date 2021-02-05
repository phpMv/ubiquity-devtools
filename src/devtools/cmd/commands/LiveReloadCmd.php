<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\cache\CacheManager;

class LiveReloadCmd extends AbstractCmd {

	public static function run(&$config, $options, $what, $activeDir) {
		$what ??= $activeDir;
		$port = self::getOption($options, 'p', 'port', 35729);
		$include = self::getOption($options, 'i', 'include', '*.php');
		$exclude = self::getOption($options, 'e', 'exclude', '{cache/*,logs/*}');

		$cmd = "livereloadx {$what} --exclude {$exclude} --include {$include} --port {$port}";

		$msg = "Starting live-reloadx server...\n";

		\exec('livereloadx --version', $foo, $exitCode);

		if ($exitCode !== 0) {
			echo ConsoleFormatter::showMessage("Problem starting livereloadx, check livereloadx installation with <b>npm install -g livereloadx</b>.\n", 'warning', 'live-reload');
			echo ConsoleFormatter::showInfo("Trying to install livereloadx\n");
			system('npm install -g livereloadx');
		}
		echo ConsoleFormatter::showInfo($msg . "Press Ctrl+C to stop it!\n");
		system($cmd . ' &');
	}
}

