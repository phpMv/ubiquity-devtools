<?php

namespace Ubiquity\devtools\cmd\commands;


use Ubiquity\devtools\cmd\Console;
use Ubiquity\themes\ThemesManager;

abstract class AbstractThemeCmd extends AbstractCmd{

	protected static function saveActiveTheme($what){
		$answer=Console::question(sprintf("Would-you like to define %s as the active theme ?",$what),['y','n']);
		if(Console::isYes($answer)){
			ThemesManager::saveActiveTheme($what);
		}
	}
}

