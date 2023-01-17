<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\utils\base\UFileSystem;

class TemplateParserCmd extends AbstractCmd {

	public static function run(&$config, $options, $templateEngines) {
		$origin = self::getOption($options, 'o', 'origin', \ROOT . \DIRECTORY_SEPARATOR . 'views' . \DIRECTORY_SEPARATOR);
		$destination = self::getOption($options, 'd', 'destination', \ROOT . \DIRECTORY_SEPARATOR . 'views' . \DIRECTORY_SEPARATOR);
		$destEngine = self::getOption($options, 'e', 'engine', 'latte');
		if (isset($templateEngines[$destEngine])) {
			$strTeEngine = $templateEngines[$destEngine]['class'];
			if (!\class_exists($strTeEngine)) {
				self::runComposer($templateEngines[$destEngine]['composer'], $destEngine);
				require_once ROOT . './../vendor/autoload.php';
			}
			$teEngine = new $strTeEngine();
			$originals = UFileSystem::glob_recursive($origin . '*.html');
			UFileSystem::safeMkdir($origin . 'back');
			foreach ($originals as $oTemplate) {
				$filename = basename($oTemplate);
				$fileContent = file_get_contents($oTemplate);
				\file_put_contents($origin . 'back' . \DIRECTORY_SEPARATOR . $filename, $fileContent);
				$code = $teEngine->generateTemplateSource($fileContent);
				\file_put_contents($destination . $filename, $code);
				echo ConsoleFormatter::showInfo("$filename parsed to $destEngine in $destination");
			}
		} else {
			echo ConsoleFormatter::showMessage("Invalid template $destEngine", 'error', 'Template parser');
		}
	}

	private static function runComposer(array $commands, string $name) {
		if (isset($commands['repositories'])) {
			$repositories = $commands['repositories'];
			foreach ($repositories as $index => $repository) {
				$type = $repository['type'];
				$url = $repository['url'];
				\system("composer config repositories.{$name}{$index} $type $url");
			}
		}
		if (isset($commands['require'])) {
			$requires = $commands['require'];
			foreach ($requires as $require => $version) {
				\system('composer require ' . $require . ':' . $version);
			}
		}
		if (isset($commands['require-dev'])) {
			$requires = $commands['require-dev'];
			foreach ($requires as $require => $version) {
				\system('composer require ' . $require . ':' . $version . ' --dev');
			}
		}
	}
}

