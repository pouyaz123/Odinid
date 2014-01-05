<?php

namespace Tools;

use Tools as T;

/**
 * Description of Cache
 *
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Settings {

	static function GetValue($LogicName) {
		static $Settings = null;
		if (!$Settings)
			$Settings = T\Cache::rabbitCache()->get("SiteSettings");
		if (!$Settings) {
			$Settings = array();
			$dt = T\DB::GetTable("SELECT `LogicName`, `Value` FROM `_app_settings`");
			foreach ($dt as $dr)
				$Settings[$dr['LogicName']] = $dr['Value'];
			T\Cache::rabbitCache()->set('SiteSettings', $Settings);
		}
		return $Settings[$LogicName];
	}

	static function GetSQLPart($LogicName) {
		return "SELECT `Value` FROM `_app_settings` WHERE `LogicName`='$LogicName'";
	}

}
