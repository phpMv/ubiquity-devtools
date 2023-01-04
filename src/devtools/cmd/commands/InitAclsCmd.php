<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\controllers\Startup;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\utils\arrays\ClassicArray;
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;
use Ubiquity\cache\CacheManager;

/**
 * Initialize ACLS from annotation in controllers.
 * Ubiquity\devtools\cmd\commands$InitAclsCmd
 *
 * @author jc
 * @version 1.0.1
 *
 */
class InitAclsCmd extends AbstractCmd {

	public static function run(&$config, $options) {
		if (! \class_exists(\Ubiquity\security\acl\AclManager::class, true)) {
			$answer = Console::question("\n\tUbiquity-acl is not available. Would you like to install it now with composer?", [
				"y",
				"n"
			]);
			if (Console::isYes($answer)) {
				\system('composer require phpmv/ubiquity-acl');
				Console::reExecute();
			} else {
				echo ConsoleFormatter::showMessage('aborted operation!', 'warning', 'init-acls');
				die();
			}
		}
		$providers = self::getOptionArray($options, 'p', 'providers', '');
		if (\is_array($providers) && \in_array('dao',$providers)) {
			$dbOffset = self::getOption($options, 'd', 'database', 'default');
			if ($dbOffset!=null) {
				\Ubiquity\security\acl\AclManager::initializeDAOProvider($config, $dbOffset);
				echo ConsoleFormatter::showMessage("ACLs tables created in $dbOffset", 'success', 'ACLs DB tables creation');
			}
			$hasModels=self::hasOption($options, 'm', 'models');
			if ($hasModels) {
				$dao=new \Ubiquity\security\acl\persistence\AclDAOProvider($config);
				$dao->createModels($dbOffset);
				$classes=$dao->getModelClasses();
				echo ConsoleFormatter::showMessage("ACLs models created", 'success', 'init-cache: models');
				$tbl=new ConsoleTable();
				$tbl->setIndent(5);
				$rArray=new ClassicArray($classes);
				$tbl->setDatas($rArray->parse());
				echo $tbl->getTable();
				ob_start();
				CacheManager::initCache($config, 'models');
				$res = ob_get_clean();
				echo ConsoleFormatter::showMessage($res, 'success', 'init-cache: models');
			}
		}


		CacheManager::start($config);
		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);
		ob_start();
		AclManager::initCache($config);
		$res = ob_get_clean();
		echo ConsoleFormatter::showMessage($res, 'success', 'init-acls');
	}
}

