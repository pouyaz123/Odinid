<?

namespace Tools;

use \Tools as T;
use \Consts as C;

/**
 * Tondarweb HTTP Tools
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
class HTTP {
	#----------------- Post Backs -----------------#

	public static function IsPostBack() {
		return isset($_SERVER['CONTENT_LENGTH']) || count($_POST); //check postback content length
	}

	public static function IsAsync() {
		return \Yii::app()->request->isAjaxRequest;
//		return \GPCS::SERVER('HTTP_X_REQUESTED_WITH') == "XMLHttpRequest" || //jQuery
//				\GPCS::POST('X_REQUESTED_WITH') == "XMLHttpRequest"; //malsup ajaxSubmit
	}

	public static function GetPostbackSize($IsErrUploadsIncluded = true) {
		$Size = 0;
		foreach ($_POST as $PVal) {
			if (!is_string($PVal))
				$PVal = implode('', $PVal);
			$Size += strlen($PVal);
		}
		foreach ($_FILES as $File) {
			if ($IsErrUploadsIncluded || $File['error'] == UPLOAD_ERR_OK)
				$Size = $File['size'];
		}
		return $Size;
	}

	/** Indicates whether this Process is on local server or not */
	private static $IsLocal = null;

	public static function IsLocal() {
		if (self::$IsLocal !== null)
			return self::$IsLocal;
		return self::$IsLocal =
				(\Conf::LocalHostName && strpos($_SERVER["SERVER_NAME"], \Conf::LocalHostName) === 0) ||
				(\Conf::LocalHostIP && strpos($_SERVER["SERVER_ADDR"], \Conf::LocalHostIP) === 0);
	}

	static function TraverseModelPostName($ReplaceSentence = null) {
		\CHtml::setModelNameConverter(function ($mixedModel)use($ReplaceSentence) {
					if (is_object($mixedModel)) {
						$PostName = method_exists($mixedModel, 'PostName') ? $mixedModel->PostName() : NULL;
						if ($PostName)
							return $PostName;
					}
					$mixedModel = is_object($mixedModel) ? get_class($mixedModel) : strval($mixedModel);
					if (!$mixedModel)
						throw new \CException("No valid model has been passed into Converter");
					if ($ReplaceSentence)
						return trim(str_replace(array($ReplaceSentence, '\\'), array('', '_'), $mixedModel), '_');
					else
						return trim(str_replace('\\', '_', $mixedModel), '_');
				});
	}

//	static function Ajax_getCActiveFormScript($FormID) {
//		return '<script>'
//				. \Yii::app()->getClientScript()->scripts[\CClientScript::POS_READY]["CActiveForm#$FormID"]
//				. '</script>';
//	}

	#----------------- About URL -----------------#

	static function URLSection($URL = NULL, $intSectionNo = 2, $strDelimiter = '/') {
		if (!$URL)
			$URL = $_SERVER['REQUEST_URI'];
		$Sections = explode($strDelimiter, $URL);
		return isset($Sections[2]) ? strtoupper($Sections[$intSectionNo]) : '';
	}

	//protocol

	public static function GetProtocol() {
		return \GPCS::SERVER('HTTPS') ? 'https' : 'http';
	}

	static function DomainName() {
		static $SN;
		if ($SN)
			return $SN;
		$SN = $_SERVER['SERVER_NAME'];
		$SN = stripos($SN, 'www.') === 0 ? substr($SN, 4) : $SN;
		return $SN;
	}

	//Domain
	public static function Get3WDomain($Protocol = null, $Domain = NULL) {
		$CurrentProtocol = strtolower(self::GetProtocol());
		if (!$Protocol)
			$Protocol = $CurrentProtocol;
		$Protocol = strtolower($Protocol);

		$W3Domain = $Protocol . '://';

		if (!T\HTTP::IsLocal()) {
			$Domain = ($Domain ? $Domain : T\HTTP::DomainName());
			$W3Domain .= (stripos($Domain, 'www.') !== 0 ? 'www.' : '') . $Domain;
		} else {//Just for my local http/https port mechanism
			$LocalPort = $_SERVER["SERVER_PORT"];
			if ($CurrentProtocol == 'http' && $Protocol == 'https')
				$LocalPort++;
			elseif ($CurrentProtocol == 'https' && $Protocol == 'http')
				$LocalPort--;
			$W3Domain.=(\Conf::LocalHostName ? \Conf::LocalHostName : (\Conf::LocalHostIP ? \Conf::LocalHostIP : 'localhost')) . ':' . $LocalPort;
		}
		return $W3Domain;
	}

	//URL+URI
	public static function GetAbsoluteURL($URI = null, $Protocol = null) {
		if (!$Protocol)
			$Protocol = self::GetProtocol();
		if (!$URI)
			$URI = $_SERVER["REQUEST_URI"];
		$URI = trim(str_replace("\\", '/', $URI), "/ ");
		return self::Get3WDomain($Protocol) . '/' . $URI;
	}

	public static function GetCurrentURL($Protocol = null) {
		return self::GetAbsoluteURL(null, $Protocol);
	}

	public static function GetCurrentURI() {
		return $_SERVER["REQUEST_URI"];
	}

	public static function Is_ValidURL($URL) {
		$Regexp = T\HTTP::IsLocal() ? C\Regexp::$URL_Local : C\Regexp::$URL;
		return preg_match($Regexp, $URL);
	}

	/**
	 * 
	 * @param str $URL
	 * @param str $strParams	//p1=v1&p2=v2
	 * @return str
	 */
	public static function URL_InsertGetParams($URL, $strParams) {
		$strParams = explode('&', $strParams);
		if (count($strParams) > 1) {
			foreach ($strParams as $strParam) {
				$URL = self::URL_InsertGetParams($URL, $strParam);
			}
			return $URL;
		}
		$strParam = $strParams[0];
		$URL = explode('#', $URL);
		$ParamKey = explode('=', $strParam, 2);
		$ParamKey = preg_quote($ParamKey[0], '/');
		if (preg_match("/[\?\&]$ParamKey=/i", $URL[0]) > 0)
			$URL[0] = preg_replace("/(\?|\&)$ParamKey=[^\&]*(\&|$)/i", "$1$strParam$2", $URL[0]);
		else
			$URL[0] = $URL[0] . (strpos($URL[0], '?') !== false ? '&' : '?') . $strParam;
		$URL = implode('#', $URL);
		return $URL;
	}

	#----------------- Headers -----------------#
	/** same as \header() but checks headers_sent() */

	public static function Header($string, $replace = true, $http_response_code = null, &$hsentfile = null, &$hsentline = null) {
		if (!\headers_sent($hsentfile, $hsentline))
			\header($string, $replace, $http_response_code);
	}

	private static $RequestHeaders = NULL;

	/**
	 * @param str if you omit, returning result will be an array of all headers
	 * @return arr/str (arr of all headers/str of special header)
	 */
	public static function RequestHeaders($HeaderName = NULL) {
		$RequestHeaders = &self::$RequestHeaders;
		if (!$RequestHeaders)
			$RequestHeaders = \F3::headers();
//		{
//			$RequestHeaders = array();
//			if (is_callable('\apache_request_headers'))
//				$RequestHeaders = \apache_request_headers();
//			elseif (is_callable('\nsapi_request_headers'))
//				$RequestHeaders = \nsapi_request_headers();
//		}
		return $HeaderName ? @$RequestHeaders[$HeaderName] : $RequestHeaders;
	}

	/** Simply Just Redirect */
	public static function Redirect($URL, $Msg = NULL, $boolRedirectWholePageOnAjax = true, $Terminate = false, $StatusCode = 302) {
		if (self::IsAsync() && $boolRedirectWholePageOnAjax) {
			echo $Msg . ' <script>window.location.href="' . addslashes($URL) . '"</script>';
			if ($Terminate)
				\Yii::app()->end();
		} else {
			\Yii::app()->request->redirect($URL, false, $StatusCode);
			if ($Msg)
				echo $Msg;
			if ($Terminate)
				\Yii::app()->end();
		}
	}

	/** Redirects Immediately and Exits and Stops Code Runtime */
	public static function Redirect_Immediately($URL, $Msg = NULL, $boolRedirectWholePageOnAjax = true, $StatusCode = 302) {
		self::Redirect($URL, $Msg, $boolRedirectWholePageOnAjax, true, $StatusCode);
	}

//
//	public static function Redirect_Permanently($URL) {
//		self::Header(C\Header::PermanentRedirect, true, C\Header::PermanentRedirectCode);
//		self::Header(C\Header::Location . "" . $URL);
//		exit;
//	}
}

?>