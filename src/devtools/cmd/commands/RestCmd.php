<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\controllers\rest\RestController;

class RestCmd extends AbstractCmdModel{
	public static function run(&$config,$options,$what){
		$resource=self::answerModel($options, 'r', 'resource', 'rest-controller', $config);
		if(class_exists($resource)){
			$scaffold=new ConsoleScaffoldController();
			$routePath=self::getOption($options, 'p', 'path','');
			$scaffold->addRestController($what,RestController::class, $resource,$routePath);
		}else{
			echo ConsoleFormatter::showMessage("The models class <b>{$resource}</b> does not exists!",'error','rest-controller');
		}
	}
}

