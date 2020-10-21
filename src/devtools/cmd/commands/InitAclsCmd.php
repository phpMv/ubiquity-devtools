<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\Command;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;

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
		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);

		AclManager::initCache($config);
		echo ConsoleFormatter::showMessage('ACLs cache initialized!', 'success', 'init-acls');
	}
}

