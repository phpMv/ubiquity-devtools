<?php

namespace Ubiquity\devtools\cmd\commands;


use Ubiquity\devtools\cmd\Console;

abstract class AbstractThemeCmd extends AbstractCmd{

	protected static function saveActiveTheme($what){
		$answer=Console::question(sprintf("Would-you like to define %s as the active theme ?",$what),['y','n']);
		if(Console::isYes($answer)){
			\Ubiquity\themes\ThemesManager::saveActiveTheme($what);
		}
	}
}

