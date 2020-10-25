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
				case 'roles':
					self::displayPart($part, \Ubiquity\security\acl\AclManager::getRoles(), [
						'name',
						'parents'
					]);
					break;
				case 'resource':
				case 'resources':
					self::displayPart($part, \Ubiquity\security\acl\AclManager::getResources(), [
						'name',
						'value'
					]);
					break;
				case 'permission':
				case 'permissions':
					self::displayPart($part, \Ubiquity\security\acl\AclManager::getPermissions(), [
						'name',
						'level'
					]);
					break;
				case 'acl':
				case 'acls':
					$acls = \Ubiquity\security\acl\AclManager::getAcls();
					self::displayPart($part, $acls, [
						'role',
						'resource',
						'permission'
					]);
					break;
				case 'map':
				case 'maps':
					self::displayPart('permissionMap', \Ubiquity\security\acl\AclManager::getPermissionMap()->getMap(), [
						'controller.action',
						'resource',
						'permission'
					]);
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
		echo ConsoleFormatter::showInfo("$count $title(s)\n");
		echo $tbl->getTable();
		if ($rArray->hasMessages()) {
			echo ConsoleFormatter::showMessage(\implode("\n", $rArray->getMessages()), 'error');
		}
	}
}

