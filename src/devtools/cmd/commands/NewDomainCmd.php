<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\domains\DDDManager;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\utils\base\UString;

class NewDomainCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		$base = self::getOption($options, 'b', 'base');
		$base = UString::cleanAttribute($base, '_');
		DDDManager::start();
		$originalBase = DDDManager::getBase();
		if (DDDManager::hasDomains() && $base !== $originalBase) {
			$rep = Console::question("The actual base for domains is <b>$originalBase</b>, which is different from <b>$base</b>.\nWould you like to rename the base to <b>$base</b>?", [
				'y',
				'n'
			]);
			if (Console::isYes($rep)) {
				DDDManager::setBase($base);
			}
		}

		$domain = UString::cleanAttribute($what, '_');
		if (! DDDManager::domainExists($domain)) {
			if (DDDManager::createDomain($domain)) {
				echo ConsoleFormatter::showMessage("The domain <b>{$domain}</b> was successfully created!", 'success', 'new-domain');
			} else {
				echo ConsoleFormatter::showMessage("There was a problem during the creation of the domain <b>{$domain}</b>!", 'error', 'new-domain');
			}
		} else {
			echo ConsoleFormatter::showMessage("The domain <b>{$domain}</b> already exists!", 'error', 'new-domain');
		}
	}
}

