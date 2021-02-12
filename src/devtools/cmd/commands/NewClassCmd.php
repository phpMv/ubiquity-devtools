<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\core\ConsoleScaffoldController;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\cache\ClassUtils;

class NewClassCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		$what = self::requiredParam($what, 'completeClassname');
		$what = \str_replace('.', "\\", $what);
		$parent = self::getOption($options, 'p', 'parent', '');
		if ($parent != null) {
			$parent = 'extends \\' . \ltrim(\str_replace('.', "\\", $parent), '\\');
		}
		$classname = ClassUtils::getClassSimpleName($what);
		$ns = ClassUtils::getNamespaceFromCompleteClassname($what);
		if ($ns != null) {
			$scaffold = new ConsoleScaffoldController();
			$scaffold->setConfig($config);
			$msg = $scaffold->_createClass('class.tpl', $classname, $ns, '', $parent, '');
			echo ConsoleFormatter::showMessage("Class <b>{$classname}</b> created!", 'success', 'new-class');
		} else {
			echo ConsoleFormatter::showMessage("Class namespace must not be empty!", 'error', 'new-class');
		}
	}
}

