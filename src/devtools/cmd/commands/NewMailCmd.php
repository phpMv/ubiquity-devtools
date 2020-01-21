<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\cache\ClassUtils;
use Ubiquity\mailer\MailerManager;

class NewMailCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		$what = self::requiredParam($what, 'classname');
		$parent = self::getOption($options, 'p', 'parent', '\\Ubiquity\\mailer\\AbstractMail');
		$hasView = self::getOption($options, 'v', 'view', false);
		$classname = ClassUtils::getClassSimpleName($what);
		$ns = MailerManager::getNamespace() . ClassUtils::getNamespaceFromCompleteClassname($what);
		$scaffold = new ConsoleScaffoldController();
		$scaffold->setConfig($config);
		$msg = $scaffold->_createClass('mailer.tpl', $classname, $ns, 'use Ubiquity\\mailer\\MailerManager;', $parent, '');
		if ($hasView) {
			$vName = $scaffold->_createViewOp('mailer', $classname);
		}
		echo ConsoleFormatter::showMessage("Mailer class <b>{$classname}</b> created!", 'success', 'new-mailer-class');
	}
}

