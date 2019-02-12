<?php
include_once 'Parameter.php';

class Command {
	protected $name;
	protected $description;
	protected $value;
	protected $aliases;
	protected $parameters;
	public function __construct($name,$value,$description,$aliases=[],$parameters=[]){
		$this->name=$name;
		$this->value=$value;
		$this->description=$description;
		$this->aliases=$aliases;
		$this->parameters=$parameters;
	}

	public function simpleString(){
		return "\t".$this->name." [".$this->value."]\t\t".$this->description;
	}

	public function longString(){
		$dec="\t";
		$result= "\n<b>".$this->name."</b> [".ConsoleFormatter::colorize($this->value,ConsoleFormatter::YELLOW)."] =>";
		$result.="\n".$dec."* ".$this->description;
		if(sizeof($this->aliases)>0){
			$result.="\n".$dec."* Aliases :";
			$aliases=$this->aliases;
			array_walk($aliases,function(&$alias){$alias="<b>".$alias."</b>";});
			$result.=" ".implode(",", $aliases);
		}
		if(sizeof($this->parameters)>0){
			$result.="\n".$dec."* Parameters :";
			foreach ($this->parameters as $param=>$content){
				$result.="\n".$dec."\t<b>-".$param."</b>";
				$result.=$content."\n";
			}
		}
		return $result;
	}

	public static function getInfo($cmd){
		$commands=self::getCommands();
		$result=[];
		foreach ($commands as $command){
			if($command->getName()==$cmd){
				$result[]=["info"=>"Command <b>{$cmd}</b> find by name","cmd"=>$command];
			}elseif(array_search($cmd, $command->getAliases())!==false){
				$result[]=["info"=>"Command <b>{$cmd}</b> find by alias","cmd"=>$command];
			}elseif(stripos($command->getDescription(), $cmd)!==false){
				$result[]=["info"=>"Command <b>{$cmd}</b> find in description","cmd"=>$command];
			}else{
				$parameters=$command->getParameters();
				foreach ($parameters as $parameter){
					if($cmd==$parameter->getName()){
						$result[]=["info"=>"Command <b>{$cmd}</b> find by the name of a parameter","cmd"=>$command];
					}
					if(stripos($parameter->getDescription(), $cmd)!==false){
						$result[]=["info"=>"Command <b>{$cmd}</b> find in parameter description","cmd"=>$command];
					}
				}
			}
		}
		return $result;
	}

	public static function project(){
		return new Command("project","projectName" ,"Creates a new #ubiquity project.",["new","create-project"],[
				"b"=>Parameter::create("dbName", "Sets the database name.", []),
				"s"=>Parameter::create("serverName", "Defines the db server address.", [],"127.0.0.1"),
				"p"=>Parameter::create("port", "Defines the db server port.", [],"3306"),
				"u"=>Parameter::create("user", "Defines the db server user.", [],"root"),
				"w"=>Parameter::create("password", "Defines the db server password.", [],""),
				"q"=>Parameter::create("phpmv", "Integrates phpmv-UI Toolkit.", ["semantic","bootstrap","ui"],""),
				"m"=>Parameter::create("all-models", "Creates all models from database.", [],""),
				"a"=>Parameter::create("admin", "Adds UbiquityMyAdmin tool.", ["true","false"],"false"),
		]);
	}

	public static function controller(){
		return new Command("controller","controllerName", "Creates a new controller.",["create-controller"],["v"=>Parameter::create("views", "creates an associated view folder", ["true","false"])]);
	}

	public static function model(){
		return new Command("model", "tableName","Generates a new model.",["create-model"]);
	}

	public static function allModels(){
		return new Command("all-models", "","Generates all models from database.",["create-all-models"]);
	}

	public static function clearCache(){
		return new Command("clear-cache", "","Clear models cache.",[],["a"=>Parameter::create("all", "Clear annotations and models cache.", ["true","false"])]);
	}

	public static function initCache(){
		return new Command("init-cache", "","Creates the cache for models.",[],[]);
	}

	public static function selfUpdate(){
		return new Command("self-update", "","Updates Ubiquity framework for the current project.",[],[]);
	}

	public static function admin(){
		return new Command("admin", "","Adds UbiquityMyAdmin webtools to the current project.",[],[]);
	}

	public static function help(){
		return new Command("help", "?","Get some help about a dev-tools command.",[],[]);
	}

	public static function crudController(){
		return new Command("crud", "","Creates a new CRUD controller.",["crud-controller"],[
				"r"=>Parameter::create("resource", "The model used", []),
				"d"=>Parameter::create("datas", "The associated Datas class", ["true","false"],"true"),
				"v"=>Parameter::create("viewer", "The associated Viewer class", ["true","false"],"true"),
				"e"=>Parameter::create("events", "The associated Events class", ["true","false"],true),
				"t"=>Parameter::create("templates", "The templates to modify", ["index","form","display"],"index,form,display"),
				"p"=>Parameter::create("path", "The associated route", []),
		]);
	}

	public static function authController(){
		return new Command("auth", "","Creates a new controller for authentification.",["auth-controller"],[
				"e"=>Parameter::create("extends", "The base class of the controller (must derived from AuthController)", [],"Ubiquity\\controllers\\auth\\AuthController"),
				"t"=>Parameter::create("templates", "The templates to modify", ["index","info","noAccess","disconnected","message","baseTemplate"],'index,info,noAccess,disconnected,message,baseTemplate'),
				"p"=>Parameter::create("path", "The associated route", []),
		]);
	}

	public static function newAction(){
		return new Command("action", "controller.model","Creates a new action in a controller.",["new-action"],[
				"p"=>Parameter::create("params", "The action parameters (or arguments)", []),
				"r"=>Parameter::create("route", "The associated route path", []),
				"v"=>Parameter::create("create-view", "Creates the associated view", [],"false"),
		]);
	}

	public static function getCommands(){
		return [self::project(),self::help(),self::controller(),self::model(),self::allModels(),self::clearCache(),self::initCache(),self::selfUpdate(),self::admin(),self::crudController(),self::authController(),self::newAction()];
	}
	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @return mixed
	 */
	public function getAliases() {
		return $this->aliases;
	}

	/**
	 * @return mixed
	 */
	public function getParameters() {
		return $this->parameters;
	}

}