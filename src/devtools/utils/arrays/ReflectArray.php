<?php
namespace Ubiquity\devtools\utils\arrays;
use Ubiquity\utils\base\UString;

class ReflectArray extends BaseArray{
	private $objects;
	private $properties;

	public function parse(){
		$object=current($this->objects);
		if(!isset($object) || !$object){
			return [['Nothing to display']];
		}
		if(!is_array($this->properties)){
			$this->properties=$this->getProperties($object);
		}
		$result=[$this->properties];
		$r=new \ReflectionClass($object);

		foreach ($this->objects as $object){
			if(is_array($object)){
				$result[]=$object;
			}elseif(is_object($object)){
				$row=[];
				foreach ($this->properties as $prop){
					if($r->hasProperty($prop)){
						$property=$r->getProperty($prop);
						$property->setAccessible(true);
						$row[]=$this->parseValue($property->getValue($object));
					}else{
						$this->messages[$prop]="Property {$prop} does not exists!";
						$row[]='';
					}
				}
				$result[]=$row;
			}
		}
		return $result;
	}

	private function parseValue($value){
		$result="-";
		if(is_array($value)){
			return $this->parseArray($value);
		}elseif(UString::isValid($value)){
			$result=var_export($value,true);
		}else{
			$result=$value;
		}
		return $result;
	}

	private function getProperties($object){
		$result=[];
		if(is_array($object)){
			return array_keys($object);
		}
		$reflect=new \ReflectionClass($object);
		$properties=$reflect->getProperties();
		foreach ($properties as $prop){
			$result[]=$prop->getName();
		}
		return $result;
	}

	/**
	 * @param mixed $objects
	 */
	public function setObjects($objects) {
		$this->objects = $objects;
	}

	/**
	 * @param multitype:NULL  $properties
	 */
	public function setProperties($properties) {
		$this->properties = $properties;
	}

}
