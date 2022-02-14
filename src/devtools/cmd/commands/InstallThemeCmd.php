<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\utils\FileUtils;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\domains\DDDManager;

class InstallThemeCmd extends AbstractThemeCmd {

	public static function run(&$config, $options, $what, $activeDir) {
		$what = self::requiredParam($what, 'themeName');
		$domain = self::updateDomain($options);
		$themesConfig = include ($activeDir . '/devtools/core/themesConfig.php');
		if (isset($themesConfig[$what])) {
			self::addTheme($what, $what, $themesConfig[$what], $activeDir, true);
		} else {
			echo ConsoleFormatter::showMessage('The theme <b>' . $what . '</b> does not exists!', 'warning', 'Themes installation');
			$answer = Console::question('Would-you like to create a new one ?', [
				'y',
				'n'
			]);
			if (Console::isYes($answer)) {
				NewThemeCmd::run($config, $options, $what, $activeDir);
			}
		}
	}

	public static function addTheme($name, $baseTheme, $themeConfig, $activeDir, $standalone = false, &$composer = []) {
		$baseDir = getcwd();
		$dest = $baseDir . '/public/assets/' . $name;
		if (! file_exists($dest)) {
			echo ConsoleFormatter::showMessage('Files copy...', 'Info', 'Adding theme ' . $name);
			FileUtils::safeMkdir($dest);
			$source = $activeDir . '/devtools/project-files/public/themes/' . $baseTheme;
			FileUtils::xcopy($source, $dest);

			if (\class_exists(\Ubiquity\domains\DDDManager::class)) {
				$dest = DDDManager::getActiveViewFolder() . 'themes/' . $name;
			} else {
				$dest = $baseDir . '/app/views/themes/' . $name;
			}
			FileUtils::safeMkdir($dest);
			$source = $activeDir . '/devtools/project-files/app/views/themes/' . $baseTheme;
			FileUtils::xcopy($source, $dest);

			$composerRequires = $themeConfig['composer'];
			foreach ($composerRequires as $composerRequire => $version) {
				if ($standalone) {
					system('composer require ' . $composerRequire);
				} else {
					$composer['require'][$composerRequire] = $version;
				}
			}
			$vendorCopies = $themeConfig['vendor-copy'] ?? [];
			if ($standalone) {
				self::copyVendorFiles($name, $vendorCopies, $baseDir);
				self::saveActiveTheme($name);
			}
			return $vendorCopies;
		}

		echo ConsoleFormatter::showMessage(\sprintf('This theme seems to be already installed in %s!', $dest), 'warning', 'Theme installation');
		return [];
	}

	public static function copyVendorFiles($theme, $vendorCopies, $baseDir) {
		foreach ($vendorCopies as $src => $dest) {
			$dest = \str_replace('%theme%', $theme, $dest);
			FileUtils::xcopy($baseDir . $src, $baseDir . $dest);
			echo ConsoleFormatter::showInfo("Copy from <b>" . $src . "</b> to <b>" . $dest . "</b>...");
		}
	}
}

