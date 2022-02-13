<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;

class AuthCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		self::updateDomain($options);
		$what = self::requiredParam($what, 'controllerName');
		$scaffold = new ConsoleScaffoldController();
		$baseClass = self::getOption($options, 'e', 'extends', "\\Ubiquity\\controllers\\auth\\AuthController");
		$authView = self::getOption($options, 't', 'templates', 'index,info,noAccess,disconnected,message,stepTwo,create,badTwoFACode,baseTemplate');
		$routePath = self::getOption($options, 'p', 'path', '');
		$scaffold->addAuthController($what, $baseClass, $authView, $routePath);
	}
}

