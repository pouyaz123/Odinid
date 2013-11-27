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
		return $Hash && $Value && self::Hash($Value, $Value2) === $Hash;
	}

}

?>
