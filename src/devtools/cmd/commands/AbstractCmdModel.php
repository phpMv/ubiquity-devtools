<?php

namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\utils\arrays\ClassicArray;
use Ubiquity\devtools\cmd\ConsoleFormatter;

class AbstractCmdModel extends AbstractCmd{
	protected static function displayModelInfo($fields,$infos,$what,$sep){
		$aFields=null;
		if($fields!=null){
			$aFields=explode(",", $fields);
		}
		if($what==null && $sep){
			$infosKeys=array_keys($infos);
			foreach ($infosKeys as $k){
				self::displayModelInfo($fields, $infos, $k, false);
			}
		}else{
			if($what!=null && isset($infos[$what])){
				$infos=$infos[$what];
			}
			$tbl=new ConsoleTable();
			$tbl->setIndent(5);
			$rArray=new ClassicArray($infos,$what);
			if(is_array($aFields) && sizeof($aFields)>0){
				$rArray->setFields($aFields);
			}
			$tbl->setDatas($rArray->parse());
			if($what!=null){
				echo ConsoleFormatter::showInfo($what);
			}
			echo $tbl->getTable();
			if($rArray->hasMessages()){
				echo ConsoleFormatter::showMessage(implode("\n", $rArray->getMessages()),'error');
			}
		}
	}

}

