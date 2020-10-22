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
					$this->displayPart($part, \Ubiquity\security\acl\AclManager::getRoles());
					break;
				case 'resource':
					$this->displayPart($part, \Ubiquity\security\acl\AclManager::getResources());
					break;
				case 'permission':
					$this->displayPart($part, \Ubiquity\security\acl\AclManager::getPermissions());
					break;
				case 'acl':
					$this->displayPart($part, \Ubiquity\security\acl\AclManager::getAcls());
					break;
				case 'map':
					$this->displayPart('permissionMap', \Ubiquity\security\acl\AclManager::getPermissionMap());
					break;
			}
		}
	}

	private function displayPart($title, $datas) {
		$count = \count($datas);
		if ($count > 0) {
			$fields = \array_keys(\current($datas));
		} else {
			$fields = [
				'elements'
			];
		}
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

