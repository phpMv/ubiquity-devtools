<?php
return [ "cdn" => [ "jquery" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js",
		"bootstrap" => [ "css" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css","js" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ],
		"semantic" => [ "css" => "https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.4/dist/semantic.min.css","js" => "https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.4/dist/semantic.min.js" ],
		"diff2html"=>["css"=>"https://cdnjs.cloudflare.com/ajax/libs/diff2html/2.12.2/diff2html.min.css"]],
		"composer" => [ "require" =>
				[ "php"=>"^7.4","twig/twig" => "^3.0","phpmv/ubiquity" => "^2.3","phpmv/ubiquity-mailer"=>"^0.0" ],
			"require-dev"=>["monolog/monolog" => "^2.0","mindplay/annotations" => "^1.3","phpmv/ubiquity-dev"=>"^0.0"],
				"autoload"=>["psr-4"=>[""=>"app/"]
				]
		]
];
