<?php

class Conf {

	/**
	 * Site module controllerMap's controllers
	 * Essentially set the controller in the <b>\Site\SiteModule</b><br/>
	 * <ul>
	 * <li>\Conf::SiteModuleControllers prevents users from registring by a username same as a Site module controller</li>
	 * <li>Also \Conf::SiteModuleControllers sets urlManager to grab routes before they go to profile controller</li>
	 * </ul>
	 * Separate controllers by pipe without additional space(it will be used in the regexp pattern of urlManager and username validation)
	 */
	const SiteModuleControllers = 'user|galleries|messages|tags|hashtags';
	//SSL
	const SSLOn_Admin = false;
	const SSLOn_Site = false;

	//SMTP
	private static $SMTP_ConnParams = array(
		'Mandrill' => array(
			'Server' => 'smtp.mandrillapp.com'
			, 'Port' => 465 //ssl:465 - tls:587
			, 'Encryption' => 'ssl'
			, 'Username' => 'pouyazm@gmail.com'
			, 'Password' => 'wJ6R_apzxGtpmrrYNFMcjA'
			, 'From' => 'pouyazm@gmail.com'
			, 'FromTitle' => 'Odinid'
			, 'HTMLAltTxt' => 'View this in a browser or program supports HTML!'
		)
	);

	static function SMTP_GetParams() {
		return self::$SMTP_ConnParams['Mandrill'];
	}

	const PHPMailer_SMTP_stream_set_timeout_On = false;

	#Cache
	#
	
	//local vs. online
	const LocalHostName = "odinid";
	const LocalHostIP = "127.0.0.";

	static function AppDir() {
		return __DIR__ . '/..';
	}

	//errors
	const Err_SecureMode = false; //Tondarweb trace system (in the UI not log)
	const Err_TraceMode = false; //Tondarweb error system (in the UI not log)
	const YiiErrsOn = true; //Turn off Yii error system to use Tondarweb tough error system instead
	const Err_AtSign_Enable = false; //Disable @ sign error reporting skip (Tondarweb error system)
	const Err_TraceLoggingOn = true; //Tondarweb trace log system (keeps the online time warnings in application/runtime/TondarwebErrorLogs)
	const Err_SysLoggingOn = false;  //Tondarweb error log system (keeps the online time errors in application/runtime/TondarwebErrorLogs)

	public static function ErrLogging_Dir() {
		return __DIR__ . '/../runtime/TondarwebErrorLogs';
	}

	//available languages (first lang will be treated as default lang)
	static $SiteModuleLangs = array('en');
	static $AdminModuleLangs = array('en');

	//other
	const AdminHomeRoute = \Admin\Consts\Routes::Cartable;
	const UserHomeRoute = \Site\Consts\Routes::UserProfile;

	static public function UserHomeRoute() {
		return \Site\Consts\Routes::UserProfile();
	}

	const jQTheme = 'redmond';

}
