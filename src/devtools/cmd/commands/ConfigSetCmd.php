<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\utils\base\UString;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\utils\base\CodeUtils;
use Ubiquity\utils\base\UArray;
use Ubiquity\controllers\Startup;

class ConfigSetCmd extends AbstractCmd{
	public static function run(&$config,$options){
		$modified=false;
		foreach ($options as $option=>$value){
			if(is_string($option)){
				$optionParts=explode(".", $option);
				$oldValue=self::getConfigOption($config, $optionParts);
				if(isset($oldValue)){
					if(UString::isValid($oldValue)){
						if($value!==$oldValue){
							self::setConfigOption($config, $optionParts, $value);
							echo ConsoleFormatter::showMessage($option." : ".var_export($oldValue,true)." -> <b>".var_export($value,true)."</b>","info");
							$modified=true;
						}
					}else{
						echo ConsoleFormatter::showMessage($option." : Unable to change a value of type object","error");
					}
				}else{
					self::setConfigOption($config, $optionParts, $value);
					echo ConsoleFormatter::showMessage($option." : inserted -> <b>".var_export($value,true)."</b>","info");
					$modified=true;
				}
			}
		}
		if($modified){
			$content="<?php\nreturn ".UArray::asPhpArray($config,"array",1,true).";";
			if(CodeUtils::isValidCode($content)){
				if(Startup::saveConfig($config)){
					echo ConsoleFormatter::showMessage("The configuration file has been successfully modified!", 'success','config:set');
				}else{
					echo ConsoleFormatter::showMessage("Impossible to write the configuration file.", 'error','config:set');
				}
			}else{
				echo ConsoleFormatter::showMessage("Your configuration contains errors.\nThe configuration file has not been saved.", 'error','config:set');
			}
		}else{
			echo ConsoleFormatter::showMessage("Nothing to update!", 'info','config:set');
		}
	}

	private static function getConfigOption($config,$optionParts){
		$c=$config;
		foreach ($optionParts as $opt){
			if(isset($c[$opt])){
				$c=$c[$opt];
			}else{
				return null;
			}
		}
		return $c;
	}

	private static function setConfigOption(&$config,$options,$value){
		$nb=sizeof($options);
		if($nb==1){
			$config[$options[0]]=$value;
		}elseif($nb>1){
			self::setConfigOption($config[$options[0]], array_slice($options, 1), $value);
		}
	}

	private static function setConfigOption_(&$config,$option,$value){
		$optionParts=explode(".", $option);
		$c=$config;
		$nb=sizeof($optionParts);
		for ($i=0;$i<$nb-1;$i++){
			$opt=$optionParts[$i];
			if(!isset($c[$opt])){
				$c[$opt]=[];
			}
			$c=$c[$opt];
		}
		$c[$optionParts[$nb-1]]=$value;
	}
}

