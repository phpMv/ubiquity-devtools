<?php

namespace Ubiquity\devtools\cmd\traits;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\cache\CacheManager;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\utils\base\UString;

trait CmdTrait {

	protected static function parseArguments(){
		global $argv;
		$argv_copy=$argv;
		array_shift($argv_copy);
		$out = array();
		foreach($argv_copy as $arg){
			if(substr($arg, 0, 2) == '--'){
				preg_match ("/\=|\:|\ /", $arg, $matches, PREG_OFFSET_CAPTURE);
				$eqPos=$matches[0][1];
				if($eqPos === false){
					$key = substr($arg, 2);
					$out[$key] = isset($out[$key]) ? $out[$key] : true;
				}
				else{
					$key = substr($arg, 2, $eqPos - 2);
					$out[$key] = substr($arg, $eqPos + 1);
				}
			}
			else if(substr($arg, 0, 1) == '-'){
				if(substr($arg, 2, 1) == '='||substr($arg, 2, 1) == ':' || substr($arg, 2, 1) == ' '){
					$key = substr($arg, 1, 1);
					$out[$key] = substr($arg, 3);
				}
				else{
					$chars = str_split(substr($arg, 1));
					foreach($chars as $char){
						$key = $char;
						$out[$key] = isset($out[$key]) ? $out[$key] : true;
					}
				}
			}
			else{
				$out[] = $arg;
			}
		}
		return $out;
	}

	protected static function getOption($options,$option,$longOption,$default=NULL){
		if(array_key_exists($option, $options)){
			$option=$options[$option];
		}else if(array_key_exists($longOption, $options)){
			$option=$options[$longOption];
		}
		else if(isset($default)===true){
			$option=$default;
		}else
			$option="";
			return $option;
	}

	protected static function getOptionArray($options,$option,$longOption,$default=NULL){
		$option=self::getOption($options, $option, $longOption,$default);
		if(is_string($option)){
			return explode(',', $option);
		}
		return $option;
	}

	protected static function getBooleanOption($options,$option,$longOption,$default=NULL){
		if(array_key_exists($option, $options)){
			$option=$options[$option];
		}else if(array_key_exists($longOption, $options)){
			$option=$options[$longOption];
		}
		else if(isset($default)===true){
			$option=$default;
		}else{
			$option=false;
		}
		if(filter_var ( $option, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) === false){
			$option=false;
		}else{
			$option=true;
		}
		return $option;
	}

	protected static function requiredParam($what,$paramName){
		if($what==null){
			ConsoleFormatter::showMessage($paramName.' is a required parameter','error');
			$answer=Console::question("Enter a value for ".$paramName.':');
			if($answer==null){
				exit(1);
			}else{
				return $answer;
			}
		}
		return $what;
	}

	protected static function answerModel($options,$option,$longOption,$part,$config){
		$resource=self::getOption($options, $option, $longOption,null);
		if($resource==null){
			echo ConsoleFormatter::showMessage($longOption.' is a required parameter! You must add <b>-'.$option.'</b> option.','error',$part);
			$models=CacheManager::getModels($config,true);
			$answer=Console::question("Enter the ".$longOption." to add from the following:\n",$models);
			if(array_search($answer, $models)!==false){
				$resource=$answer;
			}else{
				echo ConsoleFormatter::showInfo("Cancelled operation.");
				exit(1);
			}
		}
		return self::getCompleteClassname($config, $resource);
	}

	protected static function getCompleteClassname($config,$classname,$type='models'){
		$prefix=$config["mvcNS"][$type]??null;
		$classname=ltrim($classname,"\\");
		if(isset($prefix)){
			if(!UString::startswith($classname,$prefix)){
				$classname=$prefix."\\".$classname;
			}
		}
		return $classname;
	}

	protected static function getSelectedModels($models,$config){
		if($models!=null){
			$result=[];
			$models=explode(",", $models);
			foreach ($models as $model){
				$model=self::getCompleteClassname($config, $model);
				if(class_exists($model)){
					$result[]=$model;
				}
			}
			return $result;
		}
		return null;
	}
}

