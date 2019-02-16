<?php
namespace controllers;
use Ubiquity\utils\http\URequest;
use Ubiquity\controllers\Controller;
 /**
 * ControllerBase
 **/
abstract class ControllerBase extends Controller{

	public function initialize(){
		if(!URequest::isAjax()){
			$this->loadView("main/vHeader.html");
		}
	}

	public function finalize(){
		if(!URequest::isAjax()){
			$this->loadView("main/vFooter.html");
		}
	}
}
