<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\utils\base\UFileSystem;

class TemplateParserCmd extends AbstractCmd {

	public static function run(&$config, $options, $templateEngines) {
		$ds = \DIRECTORY_SEPARATOR;
		$origin = realpath(self::getOption($options, 'o', 'origin', \ROOT . $ds . 'views' . $ds));
		$destination = realpath(self::getOption($options, 'd', 'destination', \ROOT . $ds . 'views' . $ds));
		$destEngine = self::getOption($options, 'e', 'engine', 'latte');
		if (isset($templateEngines[$destEngine])) {
			$strTeEngine = $templateEngines[$destEngine]['class'];
			if (!\class_exists($strTeEngine)) {
				self::runComposer($templateEngines[$destEngine]['composer'], $destEngine);
				require_once ROOT . './../vendor/autoload.php';
			}
			$teEngine = new $strTeEngine();
			$originals = UFileSystem::glob_recursive($origin . '/*.html');

			foreach ($originals as $oTemplate) {
				$filename = basename($oTemplate);
				$oDir = dirname($oTemplate);
				$realPath = \str_replace($origin, '', $oDir);
				$fileContent = \file_get_contents($oTemplate);
				$oDest = $origin . $ds . 'back' . $ds . $realPath . $ds;
				UFileSystem::safeMkdir($oDest);
				rename($oTemplate, $oDest . $filename);
				$code = $teEngine->generateTemplateSource($fileContent);
				$dDest = $destination . $ds . $realPath . $ds;
				UFileSystem::safeMkdir($dDest);
				\file_put_contents($dDest . $filename, $code);
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

