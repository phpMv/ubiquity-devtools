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
				"cache"=>false
		],
		"namespaces"=>[],
		"templateEngine"=>'micro\\views\\engine\\Twig',
		"templateEngineOptions"=>array("cache"=>false),
		"test"=>false,
		"debug"=>false,
		"di"=>[%injections%],
		"cache"=>[""=>"cache/","system"=>"micro\\cache\\system\\ArrayCache","params"=>[]],
		"mvcNS"=>["models"=>"models","controllers"=>"controllers","rest"=>"rest"],
		"isRest"=>function(){
			return micro\utils\RequestUtils::getUrlParts()[0]==="rest";
		}
);
