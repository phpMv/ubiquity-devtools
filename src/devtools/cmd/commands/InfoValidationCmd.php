<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\utils\FrameworkParts;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class InfoValidationCmd extends AbstractCmdModel {

	public static function run(&$config, $options, $what) {
		$sep = self::getBooleanOption($options, 's', 'separate', false);
		self::updateDomain($options);
		$model = self::answerModel($options, 'm', 'model', 'info:validators', $config);
		if (\class_exists($model)) {
			$infos = FrameworkParts::getValidatorsInfo($model, $config);
			$fields = self::getOption($options, 'f', 'fields');
			self::displayModelInfo($fields, $infos, $what, $sep);
		} else {
			echo ConsoleFormatter::showMessage("The models class <b>{$model}</b> does not exists!", 'error', 'info:model');
		}
	}
}

