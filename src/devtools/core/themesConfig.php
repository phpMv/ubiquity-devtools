<?php
return [
		'bootstrap'=>[
				'composer'=>['twitter/bootstrap'=>'^4.3','fortawesome/font-awesome'=>'^5.7'],
				'vendor-copy'=>[
						'/vendor/fortawesome/font-awesome/css/all.min.css'=>'/public/assets/%theme%/css/all.min.css',
						'/vendor/fortawesome/font-awesome/webfonts'=>'/public/assets/%theme%/webfonts',
				]
		],
		'foundation'=>[
				'composer'=>['zurb/foundation'=>'^6.5']
		],
		'semantic'=>[
				'composer'=>['fomantic/ui'=>'^2.7','frameworks/jquery'=> '~2.1'],
				'vendor-copy'=>[
						'/vendor/fomantic/ui/dist/semantic.min.css'=>'/public/assets/%theme%/css/semantic.min.css',
						'/vendor/fomantic/ui/dist/semantic.min.js'=>'/public/assets/%theme%/js/semantic.min.js',
						'/vendor/frameworks/jquery/jquery.min.js'=>'/public/assets/%theme%/js/jquery.min.js'
				]
		]
];
