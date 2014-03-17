<?php

namespace Tools;

use \Tools as T;
use \Consts as C;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Security {

	private static $_AppHash = '2c7c19bd7a7ee797170729f01197c227';

	public static function Hash($Value, $Value2 = NULL) {
		return md5($Value . $Value2 . self::$_AppHash);
	}

	public static function IsValidHash($Hash, $Value, $Value2 = NULL) {
		return isset($Hash) && isset($Value) && self::Hash($Value, $Value2) === $Hash;
	}

	/**
	 * This is using HtmlPurifier component class
	 * @staticvar null $HtmlPurifier
	 * @param type $txt
	 * @param type $options
	 * @return type
	 */
	static function XSSPurify(&$txt, $options = NULL) {
		if (!$options)
			$options = array();
		$options = array_merge(array('URI.AllowedSchemes' => array(
				'http' => true,
				'https' => true,
			)), $options);
		static $HtmlPurifier = NULL;
		if (!$HtmlPurifier)
			$HtmlPurifier = new \CHtmlPurifier();
		$HtmlPurifier->options = $options;
		return $txt = $HtmlPurifier->purify($txt);
	}

	/** Currently we have used php md5() func :<br/>
	 * (PHP 4, PHP 5)<br/>
	 * Calculate the md5 hash of a string
	 * @link http://php.net/manual/en/function.md5.php
	 * @param string $str <p>
	 * The string.
	 * </p>
	 * @param bool $raw_output [optional] <p>
	 * If the optional <i>raw_output</i> is set to <b>TRUE</b>,
	 * then the md5 digest is instead returned in raw binary format with a
	 * length of 16.
	 * </p>
	 * @return string the hash as a 32-character hexadecimal number.
	 */
	static function Crypt($str, $raw_output = false) {
		return md5($str, $raw_output);
	}

}
