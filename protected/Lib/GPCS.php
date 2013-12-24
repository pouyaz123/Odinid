<?php

use \Tools as T;
use \Consts as C;

/**
 * Tondarweb Get Post Cookie Session Server and related tools
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
class GPCS {

	static function GET($ParamName) {
		if (!$ParamName)
			return null;
		//GOTO POST for a WARNING
		return isset($_GET[$ParamName]) ? $_GET[$ParamName] : NULL;
	}

	static function POST($ParamName) {
		if (!$ParamName)
			return null;
		/* it doesn't check result POST value returned by F3 because of this line:
		 * $Status = trim(\GPCS::POST('Status'))
		 * in the Admins\Lists.php
		 * if you change it to $Result ? $Result : NULL it will fail
		 */
		return isset($_POST[$ParamName]) ? $_POST[$ParamName] : NULL;
	}

	static function REQUEST($ParamName) {
		if (!$ParamName)
			return null;
		return isset($_REQUEST[$ParamName]) ? $_REQUEST[$ParamName] : NULL;
	}

	static function FILES($ParamName) {
		if (!$ParamName)
			return null;
		//GOTO POST for a WARNING
		return isset($_FILES[$ParamName]) ? $_FILES[$ParamName] : NULL;
	}

	/**
	 * 
	 * @param str $Name javascript format is enabled : cookiename.subkey is same as cookiename[subkey]<br/>
	 * this js format idea comes from F3 and namedin
	 * @param type $Value
	 * @param type $Expire
	 * @param type $Path
	 * @param type $Domain
	 * @param type $Secure
	 * @param type $httponly
	 * @return mixed cookie value | null | bool result of setting cookie
	 */
	static function COOKIE($Name, $Value = null, $Expire = 0, $Path = null, $Domain = null, $Secure = false, $httponly = false) {
		if (!$Name)
			return null;
		$Name = explode('.', str_replace(array('][', '['), '.', trim($Name, ']')));
		if (func_num_args() > 1) {
			$Name = trim($Name[0] . '[' . implode("][", array_slice($Name, 1)), '[');
			$Name .= (strpos($Name, '[') !== false ? ']' : '');
			if ($Value === NULL)
				return setcookie($Name, 'EXPIRED', time() - (365 * 24 * 3600), $Path, $Domain, $Secure, $httponly);
			else
				return setcookie($Name, $Value, $Expire, $Path, $Domain, $Secure, $httponly);
		} else {
			$Name = implode("']['", $Name);
			return eval("return (isset(\$_COOKIE['$Name']) ? \$_COOKIE['$Name'] : NULL);");
		}
	}

	static $SessionStarted = false;

	/**
	 * @param str $Name
	 * @param mixed $Value	//pass NULL explicitly for value to unset the session
	 * @return mixed session value | NULL | nothing
	 */
	static function SESSION($Name = NULL, $Value = NULL) {
		if (!self::$SessionStarted) {
			@session_start();
			self::$SessionStarted = true;
		}
		if (!$Name)
			return NULL;
		if (func_num_args() > 1) {
			if ($Value === NULL && isset($_SESSION[$Name]))
				unset($_SESSION[$Name]);
			elseif($Value !== NULL)
				$_SESSION[$Name] = $Value;
		}
		else
			return isset($_SESSION[$Name]) ? $_SESSION[$Name] : NULL;
	}

	static function SERVER($Name) {
		if (!$Name)
			return null;
		return isset($_SERVER[$Name]) ? $_SERVER[$Name] : NULL;
	}

//	public static $UploadPaths = array();

	const UPLOAD_ERR_NOTUPLOADED = 1;
	const UPLOAD_ERR_SIZE = 2;
	const UPLOAD_ERR_TYPE = 3;
	const UPLOAD_ERR_UPLOADERROR = 4;

	/**
	 *
	 * @param type $FieldName
	 * @param type $Path_withoutExt
	 * @param type $MaxSize_Bytes
	 * mytodo 3: $MaxSize_Bytes default max size should be from a var of Conf to secure
	 * @param type $PCREPattern_FileName
	 * @param type $PCREPattern_FileType
	 * @param type $arr_GPCSConstants_ErrorCode
	 * @return null|boolean (true:uploaded/false:failed/null:no file uploaded)
	 */

	/**
	 * IS NOT READY YET (incomplete migration)
	 * mytodo 2: Error Msgs in GPCS::Upload are by F3::get
	 */
	public static function UploadFile($FieldName, $Path_withoutExt, $MaxSize_Bytes = -1, $PCREPattern_FileName = NULL, $PCREPattern_FileType = NULL, &$arr_GPCSConstants_ErrorCode = array()) {
		$File = GPCS::FILES($FieldName);
		if (!$File || $File['error'] == UPLOAD_ERR_NO_FILE)
			return NULL;

		$Label = NULL;
//		$objField = F3::get($FieldName);
//		if (is_a($objField, '\Base\Form\Field')) {
//			if ($objField->Disabled())
//				return false;
//		}

		if ($File['error'] == UPLOAD_ERR_PARTIAL) {
			T\Msg::GMsg_Add(
					$arr_GPCSConstants_ErrorCode[self::UPLOAD_ERR_UPLOADERROR] = F3::get('res_IncompleteUpload', array($Label))
					, T\Msg::ErrorCSS
			);
			return false;
		}
		$MaxSize_Bytes = intval($MaxSize_Bytes);
		if (($MaxSize_Bytes > -1 && $File['size'] >= $MaxSize_Bytes) || $File['error'] == UPLOAD_ERR_INI_SIZE) {
			T\Msg::GMsg_Add(
					$arr_GPCSConstants_ErrorCode[self::UPLOAD_ERR_SIZE] = F3::get('res_UploadOversize', array($Label))
					, T\Msg::ErrorCSS
			);
			return false;
		}
		if (
				(is_string($PCREPattern_FileType) && !preg_match($PCREPattern_FileType, $File['type'])) ||
				(is_string($PCREPattern_FileName) && !preg_match($PCREPattern_FileName, $File['name']))
		) {
			T\Msg::GMsg_Add(
					$arr_GPCSConstants_ErrorCode[self::UPLOAD_ERR_TYPE] = F3::get('res_UploadInvalidType', array($Label))
					, T\Msg::ErrorCSS
			);
			return false;
		}
		if ($File['error'] > 0) {
			T\Msg::GMsg_Add(
					$arr_GPCSConstants_ErrorCode[self::UPLOAD_ERR_UPLOADERROR] = F3::get('res_UnsuccessfulUpload', array($Label))
					, T\Msg::ErrorCSS
			);
			return false;
		}
		$FileExt = explode('.', $File['name']);
		$File['EXT'] = $File['ext'] = $FileExt = strtolower(end($FileExt));

		$Path_withoutExt = rtrim($Path_withoutExt, '/\\');
		$Path = "$Path_withoutExt.$FileExt";
//		self::$UploadPaths[$Path] = true;
		move_uploaded_file($File['tmp_name'], $Path);
		return $File;
	}

}

