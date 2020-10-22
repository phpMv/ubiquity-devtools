<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\Command;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;
use Ubiquity\cache\CacheManager;

/**
 * Initialize ACLS from annotation in controllers.
 * Ubiquity\devtools\cmd\commands$InitAclsCmd
 *
 * @author jc
 * @version 1.0.0
 *
 */
class InitAclsCmd extends AbstractCmd {

	public static function run(&$config) {
		if (! \class_exists(\Ubiquity\security\acl\AclManager::class, true)) {
			$answer = Console::question("\n\tUbiquity-acl is not available. Would you like to install it now with composer?", [
				"y",
				"n"
			]);
			if (Console::isYes($answer)) {
				\system('composer require phpmv/ubiquity-acl');
			} else {
				echo ConsoleFormatter::showMessage('aborted operation!', 'warning', 'init-acls');
				die();
			}
		}
		CacheManager::start($config);
		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);

		AclManager::initCache($config);
		echo ConsoleFormatter::showMessage('ACLs cache initialized!', 'success', 'init-acls');
	}
}

