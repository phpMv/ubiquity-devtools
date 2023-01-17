<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\utils\base\UFileSystem;

class TemplateParserCmd extends AbstractCmd {

	private static array $engines = ['latte' => '\Ubiquity\views\engine\latte\Latte'];

	public static function run(&$config, $options, $what) {
		$origin = self::getOption($options, 'o', 'origin', \ROOT . \DIRECTORY_SEPARATOR . 'views' . \DIRECTORY_SEPARATOR);
		$destination = self::getOption($options, 'd', 'destination', \ROOT . \DIRECTORY_SEPARATOR . 'views' . \DIRECTORY_SEPARATOR);
		$destEngine = self::getOption($options, 'e', 'engine', 'latte');
		if (isset(self::$engines[$destEngine])) {
			$strTeEngine = self::$engines[$destEngine];
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
}

