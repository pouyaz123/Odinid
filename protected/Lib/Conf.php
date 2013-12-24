<?php

class Conf {

	const SSLOn_Admin = false;
	const SSLOn_Site = false;

	#
	//SMTP or Mandrill
	const MandrillAPIKey = 'wJ6R_apzxGtpmrrYNFMcjA';
	static $SMTP_ConnParams = array(
		'Mandrill' => array(
			'Server' => 'smtp.mandrillapp.com'
			, 'Port' => 465 //ssl:465 - tls:587
			, 'Encryption' => 'ssl'
			, 'Username' => 'pouyazm@gmail.com'
			, 'Password' => \Conf::MandrillAPIKey
			, 'From' => 'pouyazm@gmail.com'
			, 'FromTitle' => 'Odinid'
			, 'HTMLAltTxt' => 'To view this email message, open it in a program that understands HTML!'
		)
	);

	static function SMTP_GetParams() {
		return self::$SMTP_ConnParams['Mandrill'];
	}

	const SMTP_stream_set_timeout_On = false;

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
	const jQTheme = 'redmond';

}
