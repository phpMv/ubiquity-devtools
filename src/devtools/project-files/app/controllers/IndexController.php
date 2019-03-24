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

		$activeTheme=ThemesManager::getActiveTheme();
		if($activeTheme!=null){
			$themes=Display::getThemes();
			$this->loadView('@activeTheme/main/vMenu.html',compact('themes','activeTheme'));
		}
		$this->loadView($defaultPage,compact('defaultPage','links','infos','activeTheme'));
	}


	public function ct($theme){
		$config=ThemesManager::saveActiveTheme($theme);
		header("Location: ".$config['siteUrl']);
	}

}
