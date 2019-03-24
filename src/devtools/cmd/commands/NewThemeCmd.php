<?php

namespace Ubiquity\devtools\cmd\commands;


use Ubiquity\devtools\utils\FileUtils;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class NewThemeCmd extends AbstractThemeCmd{

	public static function run(&$config,$options,$what,$activeDir){
		$what=self::requiredParam($what, 'themeName');
		$themesConfig=include($activeDir."/devtools/core/themesConfig.php");
		if(!isset($themesConfig[$what])){
			$extend=self::getOption($options, 'x', 'extend');
			if($extend==null){
				self::addNew($what, $activeDir);
			}else{
				if(isset($themesConfig[$extend])){
					echo ConsoleFormatter::showInfo(sprintf('Creating a new theme <b>%s</b> from <b>%s</b>',$what,$extend));
					InstallThemeCmd::addTheme($what, $extend, $themesConfig[$extend], $activeDir,true);
				}else{
					echo ConsoleFormatter::showMessage(sprintf('The theme <b>%s</b> does not exists!',$extend),'error','Theme creation');
				}
			}
		}else{
			echo ConsoleFormatter::showMessage(sprintf('The theme <b>%s</b> is a reserved theme name!',$what),'error','Theme creation');
		}

	}

	public static function addNew($what,$activeDir){
		$msg="";
		$baseDir=getcwd();
		$dest=$baseDir.'/public/assets/'.$what;
		if(!file_exists($dest)){
			$sourceAssets=$activeDir.'/devtools/project-files/public/themes/model/';
			$sourceView=$activeDir.'/devtools/project-files/app/views/themes/model/';

			echo ConsoleFormatter::showInfo("Assets creation...");
			FileUtils::xcopy($sourceAssets, $dest);
		}else{
			$msg=sprintf("Assets creation failed : %s directory exists!\n",$dest);
		}
		$dest="app/views/themes/".$what;
		if(!file_exists($dest)){
			echo ConsoleFormatter::showInfo("Views creation...");
			FileUtils::xcopy($sourceView, $dest);
		}else{
			$msg=sprintf("Views creation failed : %s directory exists!\n",$dest);
		}
		if($msg!==""){
			echo ConsoleFormatter::showMessage($msg,"error","Theme creation");
		}else{
			self::saveActiveTheme($what);
			echo ConsoleFormatter::showMessage(sprintf('Theme %s created with success!',$what),"success","Theme creation");
		}
	}

}

