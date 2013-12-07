<?php

class Conf {

	const SSLOn_Admin = false;
	const SSLOn_Site = false;

	//available languages (first lang will be treated as default lang)
	static $SiteModuleLangs = array('en');
	static $AdminModuleLangs = array('en');

	#

	const AdminHomeRoute = \Admin\Consts\Routes::Cartable;

	#
	const LocalHostName = "odinid";
	const LocalHostIP = "127.0.0.";

	#
	const jQTheme = 'redmond';

	static function AppDir() {
		return __DIR__ . '/..';
	}

	public static function ErrLogging_Dir() {
		return __DIR__ . '/../runtime/TondarwebErrorLogs';
	}

	#

	const Err_SecureMode = false; //Tondarweb trace system (in the UI not log)
	const Err_TraceMode = false; //Tondarweb error system (in the UI not log)
	const YiiErrsOn = false; //Turn off Yii error system to use Tondarweb tough error system instead
	const Err_AtSign_Enable = false; //Disable @ sign error reporting skip (Tondarweb error system)
	const Err_TraceLoggingOn = true; //Tondarweb trace log system (keeps the online time warnings in application/runtime/TondarwebErrorLogs)
	const Err_SysLoggingOn = false;  //Tondarweb error log system (keeps the online time errors in application/runtime/TondarwebErrorLogs)

}

?>
