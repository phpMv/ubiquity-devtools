<?php
return [ "cdn" => [ "jquery" => "https://code.jquery.com/jquery-3.3.1.min.js",
		"bootstrap" => [ "css" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css","js" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ],
		"semantic" => [ "css" => "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/semantic.min.css","js" => "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/semantic.min.js" ],
		"diff2html"=>["css"=>"https://cdnjs.cloudflare.com/ajax/libs/diff2html/2.3.3/diff2html.min.css"]],
		"composer" => [ "require" =>
				[ "twig/twig" => "~1.0","mindplay/annotations" => "dev-master","phpmv/ubiquity" => "2.0.x-dev" ],
				"autoload"=>["psr-4"=>[""=>"app/"]]
		]
];
