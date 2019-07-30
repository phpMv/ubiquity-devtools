<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;

class RestApiCmd extends AbstractCmd{
	public static function run(&$config,$options,$what){
			$scaffold=new ConsoleScaffoldController();
			$routePath=self::getOption($options, 'p', 'path','');
			$scaffold->addRestApiController($what,$routePath);
	}
}

