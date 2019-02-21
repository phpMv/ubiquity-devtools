<?php

namespace Ubiquity\devtools\utils\arrays;

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

	protected function parseArray($value){
		$result=[];
		foreach ($value as $k=>$v){
			$prefix="";
			if(!is_int($k)){
				$prefix=$k." : ";
			}
			if(is_array($v)){
				$result[]=" · ".$prefix.$this->parseInlineArray($v);
			}else{
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
				$result[]=$prefix.$this->parseInlineArray($v);
			}else{
				$result[]=$prefix.$v;
			}

		}
		return '['.implode(",",$result).']';
	}

}

