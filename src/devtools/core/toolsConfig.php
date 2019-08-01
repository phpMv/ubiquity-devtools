<?php
return [ "cdn" => [ "jquery" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js",
		"bootstrap" => [ "css" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css","js" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ],
		"semantic" => [ "css" => "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css","js" => "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js" ,"state" => "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/state.min.js" ],
		"diff2html"=>["css"=>"https://cdnjs.cloudflare.com/ajax/libs/diff2html/2.3.3/diff2html.min.css"]],
		"composer" => [ "require" =>
				[ "php"=>"^7.1","twig/twig" => "^2.0","phpmv/ubiquity" => "^2.3" ],
			"require-dev"=>["monolog/monolog" => "^1.24","mindplay/annotations" => "^1.3","phpmv/ubiquity-dev"=>"^0.0"],
				"autoload"=>["psr-4"=>[""=>"app/"]
				]
		]
];
