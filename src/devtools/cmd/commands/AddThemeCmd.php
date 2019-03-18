<?php

namespace Ubiquity\devtools\cmd\commands;


use Ubiquity\devtools\utils\FileUtils;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class AddThemeCmd extends AbstractCmd{
	public static function run(&$config,$options,$what){

	}

	public static function addTheme($name,$themeConfig,$activeDir,$standalone=false,&$composer=[]){
			echo ConsoleFormatter::showMessage('Composer installation & files copy...','Info','Adding theme <b>'.$name.'</b>');

			FileUtils::safeMkdir("./../public/assets");
			$source=$activeDir.'/devtools/project-files/public/themes/'.$name;
			$dest='./../public/assets/';
			FileUtils::xcopy($source, $dest);

			FileUtils::safeMkdir("app/views/themes");
			$source=$activeDir.'/devtools/project-files/app/views/themes/'.$name;
			$dest='app/views/themes';
			FileUtils::xcopy($source, $dest);

			$composerRequires=$themeConfig['composer'];
			foreach ($composerRequires as $composerRequire=>$version){
				if($standalone){
					system("composer require ".$composerRequire);
				}else{
					$composer['require'][$composerRequire]=$version;
				}
			}
			$vendorCopies=$themeConfig['vendor-copy'];
			if($standalone){
				foreach ($vendorCopies as $src=>$dest){
					FileUtils::xcopy($src,$dest);
					echo '*';
				}
			}
			return $vendorCopies;
		}
}

