<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\utils\arrays\ClassicArray;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class ConfigCmd extends AbstractCmd{
	public static function run(&$config,$options,$what){
		echo ConsoleFormatter::showInfo('Displaying config variables from <b>app/config/config.php</b> file');
		$datas=$config;
		$fields=self::getOption($options, 'f', 'fields');
		if($fields!=null){
			$fields=explode(",", $fields);
		}
		if($what!=null && isset($config[$what])){
			$datas=$config[$what];
		}
		$tbl=new ConsoleTable();
		$tbl->setIndent(5);
		$rArray=new ClassicArray($datas,$what);
		if(is_array($fields) && sizeof($fields)>0){
			$rArray->setFields($fields);
		}
		$tbl->setDatas($rArray->parse());
		if($what!=null){
			echo ConsoleFormatter::showInfo($what);
		}
		echo $tbl->getTable();
	}
}

