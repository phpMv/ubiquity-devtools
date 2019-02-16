<?php
namespace  Ubiquity\devtools\cmd;

class Parameter {
	protected $name;
	protected $description;
	protected $values;
	protected $defaultValue;
	public function __construct($name,$description,$values,$defaultValue=""){
		$this->name=$name;
		$this->description=$description;
		$this->values=$values;
		$this->defaultValue=$defaultValue;
	}

	public function __toString(){
		$dec="\t\t\t";
		$result= "\tshortcut of --<b>".$this->name."</b>\n".$dec.$this->description;
		if(sizeof($this->values)>0){
			$result.="\n".$dec."Possibles values :";
			$result.="\n".$dec.ConsoleFormatter::colorize(implode(",", $this->values),ConsoleFormatter::DARK_GREY);
		}
		if($this->defaultValue!==""){
			$result.="\n".$dec."Default : [".ConsoleFormatter::colorize($this->defaultValue,ConsoleFormatter::GREEN)."]";
		}
		return $result;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name=$name;
		return $this;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description=$description;
		return $this;
	}

	public function getValues() {
		return $this->values;
	}

	public function setValues($values) {
		$this->values=$values;
		return $this;
	}

	public function getDefaultValue() {
		return $this->defaultValue;
	}

	public function setDefaultValue($defaultValue) {
		$this->defaultValue=$defaultValue;
		return $this;
	}

	public static function create($name,$description,$values,$defaultValue=""){
		return new Parameter($name, $description, $values,$defaultValue);
	}

}