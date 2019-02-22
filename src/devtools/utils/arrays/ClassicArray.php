<?php

namespace Ubiquity\devtools\utils\arrays;

use Ubiquity\utils\base\UArray;
use Ubiquity\utils\base\UString;

class ClassicArray extends BaseArray{
	private $fields;
	private $iFields;
	private $datas;

	public function __construct($datas,$part=null){
		if(!is_array($datas)){
			$datas=[$part=>$datas];
		}
		if(sizeof($datas)==1 && is_array(current($datas))){
			$datas=current($datas);
		}
		$this->datas=$datas;
	}

	public function parse($reverse=true){
		if($reverse){
			return $this->parseReverse_();
		}
		return $this->parse_();
	}

	private function getFields(){
		if(is_array($this->fields)){
			$fields=$this->fields;
		}else{
			$fields=array_keys($this->datas);
		}
		return $fields;
	}

	private function parseReverse_(){
		if(sizeof($this->datas)==0){
			return [['Nothing to display']];
		}
		if(UArray::isAssociative($this->datas)){
			$array=[['field','value']];
			$fields=$this->getFields();
			foreach ($fields as $field){
				if(isset($this->datas[$field]))
				$array[]=[$field,$this->parseValue($this->datas[$field])];
			}
		}else{
			$array=[];

			foreach ($this->datas as $data){
				$array[]=[$this->parseValue($data)];
			}

		}
		return $array;
	}

	private function parse_(){
		$fields=$this->getFields();
		$array=[$fields];
		$result=array_intersect_key($this->datas, array_flip($fields));
		array_walk($result, function(&$item){$item=$this->parseValue($item);});
		$array[]=$result;
		return $array;
	}

	protected function parseValue($value) {
		if(is_array($value)){
			return $this->parseArray($value,$this->iFields);
		}elseif(UString::isValid($value)){
			return var_export($value,true);
		}
		return '{.}';
	}

	/**
	 * @return mixed
	 */
	public function getDatas() {
		return $this->datas;
	}

	/**
	 * @param array $fields
	 */
	public function setFields($fields) {
		$this->fields = $fields;
	}

	/**
	 * @param mixed $datas
	 */
	public function setDatas($datas) {
		$this->datas = $datas;
	}
	/**
	 * @param mixed $iFields
	 */
	public function setIFields($iFields) {
		$this->iFields = $iFields;
	}


}

