<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\cache\CacheManager;
use Ubiquity\devtools\utils\FrameworkParts;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class InfoModelsCmd extends AbstractCmdModel{

	public static function run(&$config,$options,$what){
		$fields=self::getOption($options, 'f', 'fields','');
		$selectedModels=self::getSelectedModels(self::getOption($options, 'm', 'models',null),$config);
		CacheManager::start($config);
		$models=CacheManager::getModels($config,true);
		foreach ($models as $model){
			if($selectedModels==null || array_search($model, $selectedModels)!==false){
				$infos=FrameworkParts::getModelInfos($model,$config);
				echo ConsoleFormatter::showInfo("Infos for <b>".$model."</b>\n");
				self::displayModelInfo($fields, $infos, null, false);
			}
		}
	}
}

