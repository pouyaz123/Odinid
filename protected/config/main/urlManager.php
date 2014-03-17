<?php
return array(
	'urlFormat' => 'path',
	'showScriptName' => false,
//	'urlSuffix' => '.html', //fake suffix /asd/fgh.html for all routes
	'caseSensitive' => false, //true by default
	'rules' => array(
//		'admin/<_c:[A-z][0-z_]+>' => 'admin/<_c>',
//		'admin/<_c:[A-z][0-z_]+>/<_a:[A-z][0-z_]+>' => 'admin/<_c>/<_a>',
		'admin' => 'admin',
#
		'/' => 'site/default',
		'<_c:(' . \Conf::SiteModuleControllers . ')>/<_a:[A-z][0-z_]+>' => 'site/<_c>/<_a>',
//		'<_c:[A-z][0-z_]+>/<_a:[A-z][0-z_]+>/<lang:[a-z]{2}(_[a-z]{2})? >' => 'site/<_c>/<_a>',
#
		'<username>' => 'site/Profile',
		'<username>/<_a:[A-z][0-z_]+>' => 'site/Profile/<_a>',
	),
);
