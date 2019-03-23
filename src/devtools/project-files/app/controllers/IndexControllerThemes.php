<?php
namespace controllers;
use Ubiquity\core\postinstall\Display;
use Ubiquity\themes\ThemesManager;

 /**
 * Controller IndexController
 **/
class IndexController extends ControllerBase{

	public function index(){		
		$defaultPage=Display::getDefaultPage();
		$links=Display::getLinks();
		$infos=Display::getPageInfos();
		$themes=Display::getThemes();
		$activeTheme=ThemesManager::getActiveTheme();
		$this->loadView("index.html",compact('defaultPage','links','infos','themes','activeTheme'));
	}


	public function ct($theme){
		$config=ThemesManager::saveActiveTheme($theme);
		header("Location: ".$config['siteUrl']);
	}

}
