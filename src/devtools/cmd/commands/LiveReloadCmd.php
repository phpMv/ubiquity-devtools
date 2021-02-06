<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\cache\CacheManager;

class LiveReloadCmd extends AbstractCmd {

	public static function run(&$config, $options, $what, $activeDir) {
		$what ??= $activeDir;

		$port = self::getOption($options, 'p', 'port', 35729);
		$exts = self::getOption($options, 'e', 'exts', 'php,html');
		$exclude = self::getOption($options, 'x', 'exclusions', 'cache/,logs/');

		$cmd = "livereload {$what} --exclusions {$exclude} --exts {$exts} --port {$port}";

		$msg = "Starting live-reload server...\n";

		\exec('livereload --version', $foo, $exitCode);

		if ($exitCode !== 0) {
			echo ConsoleFormatter::showMessage("Problem starting livereload, check livereload installation with <b>npm install -g livereloadx</b>.\n", 'warning', 'live-reload');
			echo ConsoleFormatter::showInfo("Trying to install livereload\n");
			system('npm install -g livereload');
		}
		echo ConsoleFormatter::showInfo($msg . "Press Ctrl+C to stop it!\n");
		system($cmd . ' &');
	}
}

