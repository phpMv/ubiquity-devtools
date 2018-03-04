<?php
namespace controllers;
use Ubiquity\utils\http\Request;
use Ubiquity\controllers\Controller;
 /**
 * ControllerBase
 **/
abstract class ControllerBase extends Controller{

	public function initialize(){
		if(!Request::isAjax()){
			$this->loadView("main/vHeader.html");
		}
	}

	public function finalize(){
		if(!Request::isAjax()){
			$this->loadView("main/vFooter.html");
		}
	}
}
