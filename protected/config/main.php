<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
//	'name'=>'Odinid',
	'id'=>'OdYii',	//id of the application must be short. it is used for cache keys and ...
	'sourceLanguage' => 'en_us',
	#
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
		'Widgets' => 'application.Lib.Widgets',
		'Validators' => 'application.Lib.Validators',
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
	#
	'onBeginRequest' => function() {
		if (!\Conf::YiiErrsOn)
			\Err::Initialize();
		#tondarweb output system
		\Output::Initialize();
		$cs=Yii::app()->clientScript;
		$cs->scriptMap=array(
			'jquery.js'=>false,
			'jquery-ui.min.js'=>false,
		);
		//site/admin lang initiation is inside module classes
		#rabbit cache engine (local/online) : like a rabbit is fast but not persistent
		if(extension_loaded('apc')) //mytodo x: no local ApcCache : i'm on win7 & PHP5.5.6 and now Dec2013 there is no Apc ext dll for me. Also i don't know how to work with opcache. For rabbitCache , i have used File cache instead in \Tools\Cache
			\Yii::app()->components['rabbitCache'] = array(
				'class' => 'CApcCache'	//Sync this rabbitCache type with the return types in the phpDocs of the \Tools\Cache class
			);
		\CHtml::$afterRequiredLabel = '*';
	},
	#
//	'controllerPath' => YiiBase::getPathOfAlias('application.modules.Site.controllers'),
//	'controllerNamespace' => 'Site\controllers',
//	'defaultController' => 'site',	//this is the Site module here has been set for default controller
	'controllerMap' => array(
	),
	// application components
	'components' => array(
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
				'admin/<_c:[A-z][0-z_]+>' => 'admin/<_c>',
//				'admin/<_c:[A-z][0-z_]+>/<_a:[A-z][0-z_]+>' => 'admin/<_c>/<_a>',
//				'admin' => 'Admin',
				'/' => 'site/default',
				'<_c:[A-z][0-z_]+>/<_a:[A-z][0-z_]+>' => 'site/<_c>/<_a>',
//				'<_c:[A-z][0-z_]+>/<_a:[A-z][0-z_]+>/<lang:[a-z]{2}(_[a-z]{2})? >' => 'site/<_c>/<_a>',
			),
		),
		'fileCache' => array(
			'class' => 'CFileCache',
			'embedExpiry' => true,
		),
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=odinid_db',
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
		'session' => array(
			'autoStart' => false
		),
//		'veiwRenderer'=>array(
//			'class'=>'CPradoViewRenderer',
//			'fileExtension'=>'.phtml',
//		),
//		'log' => array(	//take care of the log in the preload here above
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
//		'user' => array(
//			// enable cookie-based authentication
//			'allowAutoLogin' => true,
//		),
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
	// this is used in contact page
//		'adminEmail' => 'webmaster@example.com',
	),
);