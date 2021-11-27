<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\cache\CacheManager;
use Ubiquity\devtools\cmd\commands\traits\DbCheckTrait;
use Ubiquity\devtools\utils\FrameworkParts;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class InfoModelsCmd extends AbstractCmdModel {
	use DbCheckTrait;

	public static function run(&$config, $options, $what) {
		self::updateDomain($options);
		$fields = self::getOption($options, 'f', 'fields', '');
		$dbOffset = self::getOption($options, 'd', 'database', 'default');
		self::checkDbOffset($config, $dbOffset);
		$selectedModels = self::getSelectedModels(self::getOption($options, 'm', 'models', null), $config);
		CacheManager::start($config);
		$models = CacheManager::getModels($config, true, $dbOffset);
		foreach ($models as $model) {
			if ($selectedModels == null || \array_search($model, $selectedModels) !== false) {
				$infos = FrameworkParts::getModelInfos($model, $config);
				echo ConsoleFormatter::showInfo("Infos for <b>" . $model . "</b>\n");
				self::displayModelInfo($fields, $infos, null, false);
			}
		}
	}
}
