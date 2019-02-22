<?php

namespace Ubiquity\devtools\utils\arrays;

use Ubiquity\utils\base\UIntrospection;

abstract class BaseArray {
	protected $messages=[];

	public function hasMessages(){
		return sizeof($this->messages)>0;
	}

	/**
	 * @return multitype:
	 */
	public function getMessages() {
		return $this->messages;
	}

	protected function parseArray($value,$iFields=null){
		$result=[];
		foreach ($value as $k=>$v){
			if(is_int($k) || !is_array($iFields) || array_search($k, $iFields)!==false){
				$prefix="";
				if(!is_int($k)){
					$prefix=$k." : ";
				}
				if(is_array($v)){
					$v=$this->parseInlineArray($v);
				}elseif($v instanceof \stdClass){
					$v=$this->parseInlineArray((array)$v);
				}elseif($v instanceof \Closure){
					$v=UIntrospection::closure_dump($v);
				}elseif(is_object($v)){
					$v='{o}';
				}else{
					$v=var_export($v,true);
				}
				$result[]=" · ".$prefix.$v;
			}

		}
		return implode("\n",$result);
	}

	protected function parseInlineArray($value){
		$result=[];
		foreach ($value as $k=>$v){
			$prefix="";
			if(!is_int($k)){
				$prefix=$k.": ";
			}
			if(is_array($v)){
				$v=$this->parseInlineArray($v);
			}elseif($v instanceof \stdClass){
				$v=$this->parseInlineArray((array)$v);
			}elseif($v instanceof \Closure){
				$v=UIntrospection::closure_dump($v);
			}elseif(is_object($v)){
				$v='{.}';
			}else{
				$v=var_export($v,true);
			}
			$result[]=$prefix.$v;
		}
		return '['.implode(",",$result).']';
	}

}

