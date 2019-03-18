<?php
namespace controllers;
use Ubiquity\core\postinstall\Display;

 /**
 * Controller IndexController
 **/
class IndexController extends ControllerBase{

	public function index(){
		$links=Display::getLinks();
		$infos=Display::getPageInfos();
		$this->loadView("index.html",compact('links','infos'));
	}

}
