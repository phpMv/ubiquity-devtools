<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\utils\FrameworkParts;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class InfoModelCmd extends AbstractCmdModel{

	public static function run(&$config,$options,$what){
		$sep=self::getBooleanOption($options, 's', 'separate',false);
		if($what!=null){
			$what='#'.trim($what,'#');
		}
		$model=self::answerModel($options, 'm', 'model', 'info:model', $config);
		if(class_exists($model)){
			$infos=FrameworkParts::getModelInfos($model,$config);
			$fields=self::getOption($options, 'f', 'fields');
			self::displayModelInfo($fields, $infos, $what, $sep);
		}else{
			echo ConsoleFormatter::showMessage("The models class <b>{$model}</b> does not exists!",'error','info:model');
		}
	}
}

