<?php
return [ "cdn" => [ "jquery" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js",
		"bootstrap" => [ "css" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css","js" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ],
		"semantic" => [ "css" => "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css","js" => "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js" ,"state" => "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/state.min.js" ],
		"diff2html"=>["css"=>"https://cdnjs.cloudflare.com/ajax/libs/diff2html/2.3.3/diff2html.min.css"]],
		"composer" => [ "require" =>
				[ "twig/twig" => "~1.0","mindplay/annotations" => "dev-master","phpmv/ubiquity" => "2.0.x-dev" ],
				"autoload"=>["psr-4"=>[""=>"app/"]]
		]
];
