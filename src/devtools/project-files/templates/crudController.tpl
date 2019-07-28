<?php
%namespace%
%uses%

 /**
 * CRUD Controller %controllerName%%route%
 **/
class %controllerName% extends %baseClass%{

	public function __construct(){
		parent::__construct();
		\Ubiquity\orm\DAO::start();
		$this->model="%resource%";
	}

	public function _getBaseRoute() {
		return '%routeName%';
	}
	
%content%
}
