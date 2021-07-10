<?php
namespace Ubiquity\devtools\core;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\controllers\Startup;
use Ubiquity\cache\CacheManager;
use Ubiquity\scaffolding\creators\RestControllerCreator;
use Ubiquity\controllers\rest\api\jsonapi\JsonApiRestController;
use Ubiquity\controllers\rest\api\json\JsonRestController;

class ConsoleScaffoldController extends \Ubiquity\scaffolding\ScaffoldController {

	const DELIMITER = '─';

	protected function storeControllerNameInSession($controller) {}

	private function prefixLines($str, $prefix) {
		$lines = explode("\n", $str);
		\array_walk($lines, function (&$line) use ($prefix) {
			if (trim($line) != null)
				$line = $prefix . $line;
		});
		return implode("\n", $lines);
	}

	public function showSimpleMessage($content, $type, $title = null, $icon = "info", $timeout = NULL, $staticName = null) {
		return ConsoleFormatter::showMessage($content, $type, $title);
	}

	protected function _addMessageForRouteCreation($path, $jsCallback = "") {
		echo ConsoleFormatter::showMessage("You need to re-init Router cache to apply this update with init-cache command\n");
	}

	public function addRestController($restControllerName, $baseClass, $resource, $routePath = "", $reInit = true) {
		$restCreator = new RestControllerCreator($restControllerName, $baseClass, $resource, $routePath);
		$restCreator->create($this, $reInit);
	}

	public function addRestApiController($restControllerName, $routePath = "", $reInit = true) {
		$this->addRestController($restControllerName, JsonApiRestController::class, '', $routePath, $reInit);
	}

	public function addJsonRestController($restControllerName, $routePath = "", $reInit = true) {
		$this->addRestController($restControllerName, JsonRestController::class, '', $routePath, $reInit);
	}

	public function initRestCache($refresh = true) {
		$config = Startup::getConfig();
		\ob_start();
		CacheManager::initCache($config, "rest");
		CacheManager::initCache($config, "controllers");
		$message = \ob_get_clean();
		echo $this->showSimpleMessage($message, "info", "Rest", "info cache re-init");
	}
}

