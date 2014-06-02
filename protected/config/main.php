<?php
require_once 'Conf.php';
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
		'ValidationLimits' => 'application.Lib.ValidationLimits',
	),
	'modules' => array(
//		'Admin' => array('class' => '\Admin\AdminModule'),
		'admin' => array('class' => '\Admin\AdminModule'),
//		'Site' => array('class' => '\Site\SiteModule'),
		'site' => array('class' => '\Site\SiteModule'),
//		'gii' => require_once 'main/gii.php',
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
		
		#site|admin lang initiation is inside module classes
		
		#rabbit cache engine (local/online) : like a rabbit is fast but not persistent
		if(extension_loaded('apc'))
			\Yii::app()->components['rabbitCache'] = array(
				'class' => 'CApcCache'	//Sync this rabbitCache type with the return types in the phpDocs of the \Tools\Cache class
			);
		
		#common required fields mark
		\CHtml::$afterRequiredLabel = ' *';
		
		#common PushStateScript (ajax url in HTML5 support)
		if(\Tools\HTTP::IsAsync())
			\html::PushStateScript();
		
	},
	#
//	'controllerPath' => YiiBase::getPathOfAlias('application.modules.Site.controllers'),
//	'controllerNamespace' => 'Site\controllers',
//	'defaultController' => 'site',	//this is the Site module here has been set for default controller
//	'controllerMap' => array(	#controller maps inside each module separately
//	),
	// application components
	'components' => array(
		'urlManager' => require_once 'main/urlManager.php',
		'fileCache' => array(
			'class' => 'CFileCache',
			'embedExpiry' => true,
		),
		'db' => require_once 'main/db.php',
		//error views will be read from active theme views and protected/views/system automatically
//		'errorHandler' => array(
//			// use 'site/error' action to display errors
//			'errorAction' => 'site/_errors/Error',
//		),
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
	),
	'charset'=>'UTF-8',
);