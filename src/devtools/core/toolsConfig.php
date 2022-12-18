<?php
return [
	"cdn" => [
		"jquery" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.min.js",
		"bootstrap" => [
			"css" => "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css",
			"js" => "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"
		],
		"semantic" => [
			"css" => "https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.0/dist/semantic.min.css",
			"js" => "https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.0/dist/semantic.min.js"
		],
		"diff2html" => [
			"css" => "https://cdnjs.cloudflare.com/ajax/libs/diff2html/2.12.2/diff2html.min.css"
		]
	],
	"composer" => [
		"require" => [
			"php" => ">=7.4",
			"twig/twig" => "^3.0",
			"phpmv/ubiquity" => "^2.3"
		],
		"require-dev" => [
			"monolog/monolog" => "^2.2",
			"phpmv/ubiquity-dev" => "^0.1",
			"phpmv/ubiquity-debug" => "^0.0"
		],
		"autoload" => [
			"psr-4" => [
				"" => "app/"
			]
		]
	]
];
