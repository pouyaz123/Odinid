<?php

namespace Tools;

use \Tools as T;

/**
 * Description of Cache
 *
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read integer|string $ActivationLink_LifeTime
 * @property-read integer|string $RecoveryLink_LifeTime
 * 
 * @property-read integer|string $MaxUserContacts
 * @property-read integer|string $MaxUserLocations
 * @property-read integer|string $MaxUserResidencies
 * @property-read integer|string $MaxResumeTagItemsPerCase
 * @property-read integer|string $MaxResumeBigItemsPerCase
 * 
 * @property-read integer|string $MaxProjectCats
 * @property-read integer|string $MaxProjectWorkfields
 * @property-read integer|string $MaxProjectTools
 * @property-read integer|string $MaxProjectSchool
 * @property-read integer|string $MaxProjectCompany
 */
class Settings {

	const DefaultMax = 50;

	/**
	 * @return \self
	 */
	static function GetInstance() {
		static $Inst = null;
		if (!$Inst)
			$Inst = new self;
		return $Inst;
	}

	public function __get($name) {
		return self::GetValue($name);
	}

	static function GetValue($LogicName) {
		switch ($LogicName) {
			case 'MaxResumeTagItemsPerCase':
				return 10;
				break;
			case 'MaxResumeBigItemsPerCase':
				return 5;
				break;
			case 'MaxProjectCats':
				return 50;
				break;
			case 'MaxProjectWorkfields':
				return 3;
				break;
			case 'MaxProjectTools':
				return 5;
				break;
			case 'MaxProjectSchool':
				return 2;
				break;
			case 'MaxProjectCompany':
				return 2;
				break;
			default:
				break;
		}
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
		if (isset($Settings[$LogicName]))
			return $Settings[$LogicName];
		return self::DefaultMax;
	}

	static function GetSQLPart($LogicName) {
		return "SELECT `Value` FROM `_app_settings` WHERE `LogicName`='$LogicName'";
	}

}
