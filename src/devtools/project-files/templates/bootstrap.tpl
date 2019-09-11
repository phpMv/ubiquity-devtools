<?php
use Ubiquity\devtools\cmd\ConsoleFormatter as Console;

//Comments

//For development mode initialization
function _dev(){
		echo Console::showInfo("Development mode");
}

//For Production mode initialization
function _prod(){
	echo Console::showInfo("Production mode");
}