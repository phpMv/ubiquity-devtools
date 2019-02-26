<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class RestCmd extends AbstractCmdModel{
	public static function run(&$config,$options,$what,$activeDir){
		$resource=self::answerModel($options, 'r', 'resource', 'rest-controller', $config);
		if(class_exists($resource)){
			$scaffold=new ConsoleScaffoldController($activeDir);
			$routePath=self::getOption($options, 'p', 'path','');
			$scaffold->addRestController($what, $resource,$routePath);
		}else{
			echo ConsoleFormatter::showMessage("The models class <b>{$resource}</b> does not exists!",'error','rest-controller');
		}
	}
}

