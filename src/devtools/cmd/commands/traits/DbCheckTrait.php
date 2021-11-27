<?php


namespace Ubiquity\devtools\cmd\commands\traits;


use Ubiquity\exceptions\DAOException;
use Ubiquity\orm\DAO;

trait DbCheckTrait {
	protected static function checkDbOffset(&$config,$dbOffset){
		$dbOffsetConfig=DAO::getDbOffset($config,$dbOffset);
		if(!isset($dbOffsetConfig['dbName'])){
			throw new DAOException("$dbOffset is not configured in app/config/config.php file!");
		}
	}
}