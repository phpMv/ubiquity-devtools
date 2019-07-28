<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\orm\DAO;
use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\utils\arrays\ClassicArray;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\controllers\rest\ResponseFormatter;
use Ubiquity\cache\CacheManager;

class DAOCmd extends AbstractCmd{
	public static function run(&$config,$options,$what){
		$resource=self::answerModel($options, 'r', 'resource', 'dao', $config);
		$condition=self::getOption($options, 'c', 'condition','');
		$included=self::getOptionArray($options, 'i', 'included',false);
		$fields=self::getOptionArray($options, 'f', 'fields',false);
		$parameters=self::getOptionArray($options, 'p', 'parameters',null);

		if(class_exists($resource)){
			$datas=null;
			CacheManager::startProd($config);
			DAO::start();
			$start=microtime(true);
			$objects=null;
			switch ($what){
				case 'getAll':
					$objects=DAO::getAll($resource,$condition,$included,$parameters);
					$rf=new ResponseFormatter();
					$datas=$rf->getDatas($objects);
					break;
				case 'getOne':
					$object=DAO::getOne($resource,$condition,$included,$parameters);
					$rf=new ResponseFormatter();
					$datas=$rf->cleanRestObject($object);
					break;
				case 'uGetAll':
					$objects=DAO::uGetAll($resource,$condition,$included,$parameters);
					$rf=new ResponseFormatter();
					$datas=$rf->getDatas($objects);
					break;
				case 'uGetOne':
					$object=DAO::uGetOne($resource,$condition,$included,$parameters);
					$rf=new ResponseFormatter();
					$datas=$rf->cleanRestObject($object);
					break;
				case 'count':
					$nb=DAO::count($resource,$condition,$parameters);
					echo ConsoleFormatter::showInfo($nb. " instances of ".$resource);
					break;
				case 'uCount':
					$nb=DAO::uCount($resource,$condition,$parameters);
					echo ConsoleFormatter::showInfo($nb. " instances of ".$resource);
					break;
				default:
					echo ConsoleFormatter::showMessage("Unknown command for dao : ".$what,'error','dao');
					break;
			}
			if(is_array($datas)){
				$tbl=new ConsoleTable();
				$tbl->setIndent(5);
				$rArray=new ClassicArray($datas,$what);
				if(is_array($fields) && sizeof($fields)>0){
					if(is_array($objects)){
				 		$rArray->setIFields($fields);
					}else{
						$rArray->setFields($fields);
					}
				}
				$tbl->setDatas($rArray->parse());
				if($what!=null){
					echo ConsoleFormatter::showInfo($what);
				}
				echo $tbl->getTable();
				if(is_array($objects)){
					echo ConsoleFormatter::showInfo(sizeof($datas). " instances of ".$resource);
				}
				echo ConsoleFormatter::showInfo(sprintf("Query executed in %.3f seconds", (float)microtime(true)-$start));
			}else{
				echo ConsoleFormatter::showInfo('Nothing to display');
			}
		}else{
			echo ConsoleFormatter::showMessage("The models class <b>{$resource}</b> does not exists!",'error','dao');
		}
	}
}

