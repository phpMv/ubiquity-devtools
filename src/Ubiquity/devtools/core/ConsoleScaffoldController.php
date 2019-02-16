<?php
namespace Ubiquity\devtools\core;

use Ubiquity\devtools\cmd\ConsoleFormatter;

class ConsoleScaffoldController extends \Ubiquity\scaffolding\ScaffoldController {
	private $activeDir;
	const DELIMITER = '─';

	public function __construct($activeDir) {
		$this->activeDir = $activeDir;
	}

	protected function storeControllerNameInSession($controller) {
	}

	private function prefixLines($str,$prefix){
		$lines=explode("\n", $str);
		array_walk($lines, function(&$line) use($prefix){if(trim($line)!=null) $line=$prefix.$line;});
		return implode("\n", $lines);
	}

	public function showSimpleMessage($content, $type, $title = null, $icon = "info", $timeout = NULL, $staticName = null) {
		return ConsoleFormatter::showMessage($content, $type,$title);
	}

	protected function getTemplateDir() {
		return $this->activeDir . "/project-files/templates/";
	}

	protected function _addMessageForRouteCreation($path, $jsCallback = "") {
		echo ConsoleFormatter::showMessage("You need to re-init Router cache to apply this update with init-cache command\n");
	}
}

