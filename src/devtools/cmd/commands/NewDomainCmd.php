<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\domains\DDDManager;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\utils\base\UString;

class NewDomainCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		DDDManager::start();
		$originalBase = DDDManager::getBase();
		$base = self::getOption($options, 'b', 'base', $originalBase);
		$base = UString::cleanAttribute($base, '_');
		if (DDDManager::hasDomains() && $base !== $originalBase) {
			$rep = Console::question(ConsoleFormatter::formatHtml("The actual base for domains is <b>$originalBase</b>, which is different from <b>$base</b>.\nWould you like to rename the base to <b>$base</b>?"), [
				'y',
				'n'
			]);
			if (Console::isYes($rep)) {
				if (! DDDManager::setBase($base)) {
					echo ConsoleFormatter::showMessage("There was a problem during the base renaming to <b>{$base}</b>!", 'error', 'new-domain');
				} else {
					echo ConsoleFormatter::showMessage("Domains base was renamed to <b>{$base}</b>!", 'success', 'new-domain');
				}
			}
		}

		$domain = UString::cleanAttribute($what, '_');
		if (! DDDManager::domainExists($domain)) {
			if (DDDManager::createDomain($domain)) {
				$domainBase = DDDManager::getDomainBase($domain);
				echo ConsoleFormatter::showMessage("The domain <b>{$domain}</b> was successfully created in <b>$domainBase</b>!", 'success', 'new-domain');
			} else {
				echo ConsoleFormatter::showMessage("There was a problem during the creation of the domain <b>{$domain}</b>!", 'error', 'new-domain');
			}
		} else {
			echo ConsoleFormatter::showMessage("The domain <b>{$domain}</b> already exists!", 'error', 'new-domain');
		}
	}
}

