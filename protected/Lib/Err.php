<?php

use \Tools as T;
use \Consts as C;

/**
 * Tondarweb Error & Debugging Tools and Handlers
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal migrated to Odinid
 * @version 2
 * @copyright (c) Odinid
 * @access public
 */
class Err extends CException {

	public function __construct($MethodName, $message, $mixedDetails = "No Details", $code = 500) {
		parent::__construct(
				self::ErrMsg_Method($MethodName, $message, $mixedDetails, true, false)
				, $code
		);
	}

	public static function Initialize() {
		if (!\Conf::YiiErrsOn)
			set_error_handler("Err::ErrMsg_MainHandler");
	}

//	private static $BreakPointsCount = 0;
//
//	public static function Terminate() {
//		if (!T\HTTP::IsLocal() && !Conf::Err_SecureMode && !T\HTTP::IsAsync())
//			self::ErrMsg('<div>!! Security Risk for Conf::Err_SecureMode !!</div>');
//		if (self::$BreakPointsCount > 0)
//			echo '<div>there is ' . self::$BreakPointsCount . ' number of loose breakpoints within codes</div>';
//	}
#----------------- Errors -----------------#

	public static function ErrMsg($strMessage = "", $mixedDetails = "No Details", $boolReturn = false, $BreakCode = true, $Add500ErrCodeInHeader = true) {
		//Logging
		self::AppendLogMsg("SYS", $strMessage, $mixedDetails);

		$Code = C\Header::ServerInternalErrorCode;
		if ($Add500ErrCodeInHeader)
			T\HTTP::Header(C\Header::ServerInternalError, true, $Code);

		if (Conf::Err_SecureMode) {
			$strMessage = "A System Error or Trace Msg Occurred!(Secure Mode is ON. check logs!)";
			$mixedDetails = NULL;
		} else { //mytodo x: html encoding for err details
			$mixedDetails = !is_string($mixedDetails) ? print_r($mixedDetails, true) : $mixedDetails;
			$DetailsBoxHeight='15px';
			$mixedDetails = "<div style='height:$DetailsBoxHeight; overflow:hidden; padding: 2px; cursor:default; background:#400; color:#fee; font-family:Lucida Console'"
					. " onclick='if(!this.v){this.style.height=\"auto\"; this.style.overflow=\"auto\"; this.v=1}else{this.style.height=\"$DetailsBoxHeight\"; this.style.overflow=\"hidden\"; this.v=0}'>"
					. " $mixedDetails "
					. "</div>";
		}

		$strMessage = "<div style='background:#fee; color:#400; border:1px solid #400; padding:2px; margin:3px 0px; direction:ltr; text-align:left'>"
				. "\n<!--##--ERROR--##-->\n"
				. $strMessage
				. " <pre style=\"margin:0px; overflow:auto\"> "
				. $mixedDetails
				. " </pre>"
				. "</div>";
		if ($BreakCode) {
			throw new \CException($strMessage, $Code);
//			exit;
		}
		if ($boolReturn)
			return $strMessage;
		else
			echo $strMessage;
	}

	public static function ErrMsg_Method($MethodName, $strMsg, $mixedDetails = "No Details", $boolReturn = false, $BreakCode = true) {
		return self::ErrMsg("$MethodName : $strMsg", $mixedDetails, $boolReturn, $BreakCode);
	}

	public static function ErrMsg_MainHandler($ENO, $EStr, $FileN, $LineNO, $EContext) {
		//with @ sign show no error msg(off err reporting)
		if (( error_reporting() != 0 || !Conf::Err_AtSign_Enable)) { //error_reporting() != 0 (@Event)
			$Msg = " Place	: <b>$FileN</b> (Line:<b>$LineNO</b> - Error NO : $ENO) <br/> Desc	: $EStr <br/>";
			#more details
			self::ErrMsg("PHP Script Fatal Error<br/>$Msg", $EContext);
			exit;
		}
		//return false; //continue with php err handler
	}

#----------------- Tracing And Debug -----------------#
	/** Displays Msg only at trace mode */

	public static function TraceMsg($strMessage = "", $mixedDetails = "No Details", $boolReturn = false) {
		$strMessage = "<b>(Trace)</b> $strMessage";

		//Logging
		self::AppendLogMsg("TRACE", $strMessage, $mixedDetails);

		if (Conf::Err_TraceMode)
			return self::ErrMsg($strMessage, $mixedDetails, $boolReturn, false, false);
	}

	public static function TraceMsg_Method($MethodName, $strMsg, $mixedDetails = "No Details", $boolReturn = false) {
		return self::TraceMsg("$MethodName : $strMsg", $mixedDetails, $boolReturn);
	}

	/** Just Developing and Debugging time */
	public static function DebugBreakPointMsg($Msg, $Break = true) {
//		self::$BreakPointsCount++;
		$Msg = is_array($Msg) || is_object($Msg) ? print_r($Msg, 1) : $Msg;
		T\HTTP::Header(C\Header::ServerInternalError, true, C\Header::ServerInternalErrorCode);
		echo "
<div style='direction:ltr; text-align:left; background:#fff'>
	<!--##--ERROR--##-->
	<span style='background:#a00; color:#fff'>(Break Point Msg)</span> : 
	<pre style='border:1px solid #000; padding:3px; overflow:auto'>
		$Msg
	</pre>
</div>";

		if ($Break)
			exit;
	}

	public static function DebugBreakPoint($Msg, $Break = true) {
		self::DebugBreakPointMsg($Msg, $Break);
	}

#----------------- Logging -----------------#

	private static function AppendLogMsg($LogType, $strMessage, $mixedDetails = null) {
		if (($LogType == "SYS" && Conf::Err_SysLoggingOn == FALSE) ||
				($LogType == "TRACE" && Conf::Err_TraceLoggingOn == FALSE))
			return;

		$LogExt = $LogType == "SYS" ? ".sys.log" : ".trace.log";
		file_put_contents(
				Conf::ErrLogging_Dir() . '/' . gmdate('Y-m-d H_i_s') . $LogExt
				, $strMessage . "\r\n" . print_r($mixedDetails, true) . "\r\n------------\r\n\r\n"
				, FILE_APPEND);
	}

	/**
	 * Page_InsecureHTTPS IS A ROUTING METHOD. DON'T CALL THIS
	 */
//	static function Page_InsecureHTTPS() {
//		if (T\Security::SSL_CheckCertificate(TRUE)) {
//			$Referrer = \GPCS::GET('referrer');
//			$URL = $Referrer ? $Referrer : \Routing::GetURL(\RouteKeys::Home);
//			T\HTTP::Redirect_Immediately($URL);
//		}
//		\F3::set('tplPage', 'MsgPages/InsecureConnection.tpl');
//		\F3::set('headTitle', '{{@res_Insecure_Title}}');
//		T\HTTP::Header(C\Header::Forbidden, true, C\Header::ForbiddenCode);
//		echo \Template::serve('MsgPages/Master.tpl');
//		exit;
//	}
//	static function F3_ErrorHandler() {
////		Language::SetAppLang(Language::GetDefaultLangRow(NULL, \GPCS::COOKIE('DefLng')));
//		\Language::CheckLang(true);
//		if (\F3::get('IsNamedIn'))
//			\F3::set('res_Copyright', '{{@res_KASP_Copyright}}');
//		$Code = F3::get('ERROR.code');
//		switch ($Code) {
//			case 404:
//				\F3::set('urlHomeEn', \Routing::GetURL_FullCondition(\RouteKeys::Home, 'http', 'En'));
//				\F3::set('urlHomeFa', (\Language::$Code ? \Routing::GetURL_FullCondition(\RouteKeys::Home, 'http', \Language::$Code) : '#'));
//				\F3::set('tplPage', 'MsgPages/404.tpl');
//				\F3::set('headTitle', '{{@res_404_Title}}');
//				T\HTTP::Header(C\Header::NotFound, true, C\Header::NotFoundCode);
//				echo \Template::serve('MsgPages/Master.tpl');
//				break;
//			default:
//				Err::ErrMsg('Err traced by F3!', F3::get('ERROR'));
//				break;
//		}
//		exit(); //for certainty
//	}
//	static function HotLink() {
//		F3::error(404); //mytodo 3: hot linking page
//	}
//	static function ForbiddenAction_Admin($ActionDesc = NULL) {
//		T\HTTP::Header(C\Header::Forbidden, true, C\Header::ForbiddenCode);
//		$ActionDesc = $ActionDesc ? \F3::resolve($ActionDesc) : '';
//		self::ErrPage_Admin("{{@res_ForbiddenAction}} : $ActionDesc");
//		exit;
//	}
//	static function ErrPage_Admin($Msg) {
//		\F3::set('ErrPageMsg', $Msg);
//		if (T\HTTP::IsAsync())
//			echo \F3::resolve($Msg);
//		else {
//			\F3::set('tplPage', 'MsgPages/ErrPage_Admin.tpl');
//			echo Template::serve('{{@tplMaster}}');
//		}
//		exit;
//	}
}
