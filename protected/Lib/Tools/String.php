<?php

namespace Tools;

use Consts as C;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @access public
 */
class String {

	const Chrset1_HTML = 'iso-8859-1';
	const Chrset2_HTML = 'utf-8';
	const Chrset1_DB = 'ascii';
	const Chrset2_DB = 'utf8';

	static function Encode2_DB($val) {
		return mb_convert_encoding($val, self::Chrset2_DB, self::Chrset2_DB);
	}

	#----------------- Strings -----------------#
//	static function MyIndexOf($str, $find, $ofEnd = false) {
//		return $ofEnd ? strpos($str, $find) : strrpos($str, $find);
//	}
//
//	static function FindAfter($str, $find, $after, $ofEnd = false) {
//		$fromWhr = self::MyIndexOf($str, $after, $ofEnd);
//		return $fromWhr !== false ? strpos($str, $find, $fromWhr) : false;
//	}

	static function Find2Find_substr($Str, $findStart, $findEnd = null) {
		$Str = explode($findStart, $Str, 2);
		if (!isset($Str[1]))
			return null;
		if (!$findEnd)
			return $Str[1];
		$Str = explode($findEnd, $Str[1], 2);
		return isset($Str[0]) ? $Str[0] : null;
//		$fromWhr = self::MyIndexOf($str, $findStart, $ofEnd);
//		$toWhr = self::FindAfter($str, $findEnd, $findStart, $ofEnd);
//		if ($fromWhr === false)
//			return '';
//		if ($toWhr === false)
//			$toWhr = strlen($str);
////return substring(fromWhr + findStart.length, toWhr)
//		return 
	}

	static function ucwords_ASCIISafe($str) {
		return preg_match(C\Regexp::ASCIIChars_Simple, $str) ? ucwords($str) : $str;
	}

//	static function GetHandledMasterTitle() {
//		$headTitle = \F3::get('headTitle');
//		$MainTitle = \F3::get('res_MainTitle');
//		return $MainTitle . ($MainTitle && $headTitle ? ' | ' : '') . $headTitle;
//	}

	static function str_ReplaceRecursively($Search, $Replace, $HayStack) {
		while (strpos($HayStack, $Search) !== false)
			$HayStack = str_replace($Search, $Replace, $HayStack);
		return $HayStack;
	}

//	static function str_iReplaceRecursively($Search, $Replace, $HayStack) {
//		while (stripos($HayStack, $Search) !== false)
//			$HayStack = str_ireplace($Search, $Replace, $HayStack);
//		return $HayStack;
//	}

	private static function InjectParams_Recursive($arrStrings, $ParamKeys, $ParamValues, $fncCallback, $Pattern) {
		if (count($ParamKeys) == 0)
			return $arrStrings;
		$Key = array_shift($ParamKeys);
		if (is_string($Key) && $Key[0] == ':')
			$Key = substr($Key, 1);
		$Value = array_shift($ParamValues);
		if ($fncCallback)
			$Value = $fncCallback($Key, $Value);
		foreach ($arrStrings as $Idx => $Str) {
			if (strlen($Str) == 0)
				continue;

			$Str = preg_split($Pattern['OPEN'] . $Key . $Pattern['CLOSE'], $Str);
			$Str = self::InjectParams_Recursive($Str, $ParamKeys, $ParamValues, $fncCallback, $Pattern);

			$Str = implode($Value, $Str);
			$arrStrings[$Idx] = $Str;
		}
		return $arrStrings;
	}

	/**
	 *
	 * @param str $Str
	 * @param arr $arrParams
	 * @param fnc $fncCallback //function($Key, $Value)
	 * @param mixed $AbsentParamReplacer	//NULL:NO ABSENT REPLACEMENT
	 * @param arr $Pattern
	 * @return str 
	 */
	public static function InjectParams($Str, $arrParams, $fncCallback = null, $AbsentParamReplacer = NULL
	, $Pattern = array('OPEN' => '/\:', 'CLOSE' => '(?!\w)/')
	) {
		if (is_string($Str) && is_array($arrParams)) {
			$Str = self::InjectParams_Recursive(array($Str), array_keys($arrParams), array_values($arrParams), $fncCallback, $Pattern);
			$Str = $Str[0];
			if ($AbsentParamReplacer !== NULL && $AbsentParamReplacer !== FALSE)
				$Str = preg_replace($Pattern['OPEN'] . '\w+' . $Pattern['CLOSE'], $AbsentParamReplacer, $Str);
		}
		return $Str;
	}

	/**
	 * 1000 -> 1,000
	 * @param int|str $Digits
	 * @return string
	 */
//	money_format
//	static function CurrencyDigitMaker($Digits) {
//		if (!$Digits || !($Digits = intval($Digits)))
//			return $Digits;
//		$Digits.='';
//		$Len = strlen($Digits);
//		if (!$Len || $Len <= 3)
//			return $Digits;
//		$commas = floor($Len / 3);
//		$result = '';
//		for ($i = 0; $i < $commas; $i++) {
//			$result = substr($Digits . '', -3) . ($result ? ',' : '') + $result;
//			$Digits = substr($Digits, 0, strlen($Digits) - 3);
//		}
//		$result = $Digits + ($Digits ? ',' : '') + $result;
//		return $result;
//	}
//
//	/**
//	 * 1000 -> 1K
//	 * @param int|str $Digits
//	 * @return string
//	 */
//	static function KMGMaker($Digits, $Zeros = 1000) {
//		if (!$Digits || !($Digits = intval($Digits)))
//			return $Digits;
//		$arr = array('K', 'M', 'G', 'T', '-1' => '');
//		$Unit = -1;
//		while ($Digits >= $Zeros) {
//			$Digits = $Digits / $Zeros;
//			$Unit++;
//		}
//		return round($Digits, 1, PHP_ROUND_HALF_DOWN) . $arr[$Unit];
//	}
	/**
	 * @param string $String
	 * @param string $Delimiter
	 * @return array
	 */
	public static function SafeExplode($String, $Delimiter = ',') {
		if (!$String)
			return $String;
		return explode($Delimiter, trim($String, "$Delimiter\t\n\r\0\x0B"));
	}

}
