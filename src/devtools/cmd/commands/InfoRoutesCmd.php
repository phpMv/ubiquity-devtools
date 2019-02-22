<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\utils\FrameworkParts;
use Ubiquity\devtools\utils\arrays\ReflectArray;

class InfoRoutesCmd extends AbstractCmd{
	public static function run(&$config,$options,$what){
		$type=self::getOption($options, 't', 'type','routes');
		$fields=self::getOption($options, 'f', 'fields','path,controller,action,parameters');
		$limit=self::getOption($options, 'l', 'limit');
		$offset=self::getOption($options, 'o', 'offset');
		$search=self::getOption($options, 's', 'search');
		$method=self::getOption($options, 'm', 'method');
		$tbl=new ConsoleTable();
		if($search!=null || $method!=null){
			$search=($search==null)?null:$search;
			$routes=FrameworkParts::testRoutes($config, $search,$method,$type);
		}else{
			$routes=FrameworkParts::getRoutes($config,$type,$method);
		}

		if($limit!=null || $offset!=null){
			$offset=$offset?(int)$offset:0;
			$limit=($limit)?(int)$limit:null;
			$routes=array_slice($routes, $offset,$limit);
		}
		$rArray=new ReflectArray();
		$rArray->setProperties(explode(",", $fields));
		$rArray->setObjects($routes);
		$tbl->setDatas($rArray->parse());
		echo $tbl->getTable();
		if($rArray->hasMessages()){
			echo ConsoleFormatter::showMessage(implode("\n", $rArray->getMessages()),'error');
		}
		echo ConsoleFormatter::showInfo(sizeof($routes)." routes ({$type})\n");
	}
}

