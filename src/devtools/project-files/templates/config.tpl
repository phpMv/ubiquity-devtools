<?php
return array(
		"siteUrl"=>"%siteUrl%",
		"database"=>[
				"type"=>"%dbType%",
				"wrapper"=>"%dbWrapper%",
				"dbName"=>getenv('DB_NAME'),
				"serverName"=>"%serverName%",
				"port"=>"%port%",
				"user"=>getenv('DB_USER'),
				"password"=>getenv('DB_PASS'),
				"options"=>[],
				"cache"=>false
		],
		"sessionName"=>"%sessionName%",
		"namespaces"=>[],
		"templateEngine"=>'Ubiquity\\views\\engine\\Twig',
		"templateEngineOptions"=>array("cache"=>false%activeTheme%),
		"test"=>false,
		"debug"=>false,
		"logger"=>function(){return new \Ubiquity\log\libraries\UMonolog("%projectName%",\Monolog\Logger::INFO);},
		"di"=>[%injections%],
		"cache"=>["directory"=>"cache/","system"=>"Ubiquity\\cache\\system\\ArrayCache","params"=>[]],
		"mvcNS"=>["models"=>"models","controllers"=>"controllers","rest"=>""]
);
