<?php
return array(
		"siteUrl"=>"%siteUrl%",
		"database"=>[
				"type"=>"%dbType%",
				"dbName"=>"%dbName%",
				"serverName"=>"%serverName%",
				"port"=>"%port%",
				"user"=>"%user%",
				"password"=>"%password%",
				"options"=>[],
				"cache"=>false
		],
		"sessionName"=>"%projectName%",
		"namespaces"=>[],
		"templateEngine"=>'Ubiquity\\views\\engine\\Twig',
		"templateEngineOptions"=>array("cache"=>false%activeTheme%),
		"test"=>false,
		"debug"=>false,
		"logger"=>function(){return new \Ubiquity\log\libraries\UMonolog("%projectName%",\Monolog\Logger::INFO);},
		"di"=>[%injections%],
		"cache"=>["directory"=>"cache/","system"=>"Ubiquity\\cache\\system\\ArrayCache","params"=>[]],
		"mvcNS"=>["models"=>"models","controllers"=>"controllers","rest"=>""],
		"isRest"=>function(){
			return \Ubiquity\utils\http\URequest::getUrlParts()[0]==="rest";
		}
);
