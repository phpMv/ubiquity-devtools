<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\utils\FrameworkParts;
use Ubiquity\devtools\utils\arrays\ReflectArray;

class DisplayAclsCmd extends AbstractCmd {

	public static function run(&$config, $options) {
		$part = self::getOption($options, 'v', 'value', 'all');
		if ($part === 'all') {
			$parts = [
				'role',
				'permission',
				'resource',
				'acl',
				'map'
			];
		} else {
			$parts = [
				$part
			];
		}
		\Ubiquity\cache\CacheManager::start($config);
		\Ubiquity\security\acl\AclManager::start();
		\Ubiquity\security\acl\AclManager::initFromProviders([
			new \Ubiquity\security\acl\persistence\AclCacheProvider()
		]);
		foreach ($parts as $part) {
			switch ($part) {
				case 'role':
					self::displayPart($part, \Ubiquity\security\acl\AclManager::getRoles(), [
						'name',
						'parents'
					]);
					break;
				case 'resource':
					self::displayPart($part, \Ubiquity\security\acl\AclManager::getResources(), [
						'name',
						'value'
					]);
					break;
				case 'permission':
					self::displayPart($part, \Ubiquity\security\acl\AclManager::getPermissions(), [
						'name',
						'level'
					]);
					break;
				case 'acl':
					$acls = \Ubiquity\security\acl\AclManager::getAcls();
					$aclsArray = [];
					foreach ($acls as $acl) {
						$aclsArray[] = [
							'role' => $acl->getRole()->getName(),
							'resource' => $acl->getResource()->getName(),
							'permission' => $acl->getPermission()->getName()
						];
					}
					self::displayPart($part, $aclsArray, [
						'role',
						'resource',
						'permission'
					]);
					break;
				case 'map':
					// self::displayPart('permissionMap', \Ubiquity\security\acl\AclManager::getPermissionMap()->getMap(), []);
					break;
			}
		}
	}

	private static function displayPart($title, $datas, $fields) {
		$count = \count($datas);
		$tbl = new ConsoleTable();
		$rArray = new ReflectArray();
		$rArray->setProperties($fields);
		$rArray->setObjects($datas);
		$tbl->setDatas($rArray->parse());
		echo $tbl->getTable();
		if ($rArray->hasMessages()) {
			echo ConsoleFormatter::showMessage(\implode("\n", $rArray->getMessages()), 'error');
		}
		echo ConsoleFormatter::showInfo("$count $title(s)\n");
	}
}

