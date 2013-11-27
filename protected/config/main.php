<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
//	'name'=>'Odinid',
	'defaultController' => 'Site/Site',
	'sourceLanguage' => 'en_us',
	// preloading 'log' component
//	'preload' => array('log'),
	// autoloading model and component classes
	'import' => array(
//		'application.models.*',
//		'application.components.*',
		'application.Lib.*',
	),
	'aliases' => array(
		'Site' => 'application.modules.Site',
		'Admin' => 'application.modules.Admin',
		//MyLib
		'Base' => 'application.Lib.Base',
		'Consts' => 'application.Lib.Consts',
		'Interfaces' => 'application.Lib.Interfaces',
		'Tools' => 'application.Lib.Tools',
	),
	'modules' => array(
//		'Admin' => array('class' => '\Admin\AdminModule'),
		'admin' => array('class' => '\Admin\AdminModule'),
//		'Site' => array('class' => '\Site\SiteModule'),
		'site' => array('class' => '\Site\SiteModule'),
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => "vlc'd]di?",
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters' => array('127.0.0.1'),
		),
	),
	'controllerMap' => array(
	),
	'onBeginRequest' => function() {
		if (!\Conf::YiiErrsOn)
			\Err::Initialize();
		#tondarweb output system
		\Output::Initialize();
	},
	// application components
	'components' => array(
//		'user' => array(
//			// enable cookie-based authentication
//			'allowAutoLogin' => true,
//		),
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
				'admin/<_c:[A-z][0-z_]+>' => 'admin/<_c>',
//				'admin/<_c:[A-z][0-z_]+>/<_a:[A-z][0-z_]+>' => 'admin/<_c>/<_a>',
//				'admin' => 'Admin',
				'<_c:[A-z][0-z_]+>/<_a:[A-z][0-z_]+>' => 'site/<_c>/<_a>',
//				'<_c:[A-z][0-z_]+>/<_a:[A-z][0-z_]+>/<lang:[a-z]{2}(_[a-z]{2})? >' => 'site/<_c>/<_a>',
			),
		),
		'session' => array(
			'autoStart' => false
		),
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=cgnetwork',
			'emulatePrepare' => true,
			'username' => 'FreeUN',
			'password' => 'FreePWS',
			'charset' => 'utf8',
			'tablePrefix' => '_'
		),
		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => 'site/_errors/Error',
		),
//		'log' => array(
//			'class' => 'CLogRouter',
//			'routes' => array(
			//Logging file in runtime directory
//				array(
//					'class'=>'CFileLogRoute',
//					'levels'=>'error, warning',
//				),
			//TRACING
			// uncomment the following to show log messages on web pages
//				array(
//					'class'=>'CWebLogRoute',
//				),
//			),
//		),
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
	// this is used in contact page
//		'adminEmail' => 'webmaster@example.com',
	),
);