<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class CrudCmd extends AbstractCmdModel{
	public static function run(&$config,$options,$what){
		$resource=self::answerModel($options, 'r', 'resource', 'crud-controller', $config);
		if(class_exists($resource)){
			$scaffold=new ConsoleScaffoldController();
			$crudDatas=self::getOption($options, 'd', 'datas',true);
			$crudViewer=self::getOption($options, 'v', 'viewer',true);
			$crudEvents=self::getOption($options, 'e', 'events',true);
			$crudViews=self::getOption($options, 't', 'templates','index,form,display');
			$routePath=self::getOption($options, 'p', 'path','');
			$scaffold->addCrudController($what, $resource,$crudDatas,$crudViewer,$crudEvents,$crudViews,$routePath);
		}else{
			echo ConsoleFormatter::showMessage("The models class <b>{$resource}</b> does not exists!",'error','crud-controller');
		}
	}
}

