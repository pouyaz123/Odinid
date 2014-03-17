<?php

namespace Tools;

use \Tools as T;
use \Consts as C;

/**
 * User authenticate tools (admin or site)
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb portal migrated to Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class UserAuthenticate {

	const CookieIDKey = 'id';
	const CookieHashKey = 'h';
	const CookieVHashKey = 'vh';
	const Secure = false;
	const RememberHours = 48;

	static function MakeItLoggedIn(
	$SessionName
	, $drUser
	, $boolRemember
	, $CookieName
	, $UserID
	, $Username
	, $LoginIP
	, $LoginTime
	, $RememberHours = self::RememberHours
	, $boolSecure = self::Secure
	, $CookieIDKey = self::CookieIDKey
	, $CookieHashKey = self::CookieHashKey
	, $CookieVHashKey = self::CookieVHashKey) {
		session_set_cookie_params(0, NULL, NULL, $boolSecure);
		\GPCS::SESSION($SessionName, $drUser);
		if ($boolRemember) {
			$Expire = $LoginTime + ( $RememberHours * 60 * 60 );
			$Hash = self::GetHash($Username, $LoginTime, $LoginIP);
			\GPCS::COOKIE($CookieName . "[$CookieIDKey]"
					, $UserID, $Expire, '/', null, $boolSecure, true);
			\GPCS::COOKIE($CookieName . "[$CookieHashKey]"
					, $Hash, $Expire, '/', null, $boolSecure, true);
			//set the vhash : vhash is to validate against user id to prevent invalid user login posts from creating overload SQL communication
			\GPCS::COOKIE($CookieName . "[$CookieVHashKey]"
					, T\Security::Hash($UserID), $Expire, '/', null, $boolSecure, true);
		}
	}

	static function GetHash($Username, $LoginTime, $LoginIP) {
		return T\Security::Hash($LoginIP . $LoginTime, $Username);
	}

	static function IsValidHash($CookieName, $ID, $CookieVHashKey = self::CookieVHashKey) {
		$VHash = \GPCS::COOKIE("$CookieName." . $CookieVHashKey);
		return T\Security::IsValidHash($VHash, $ID);
	}

	static function Logout(
	$SessionName
	, $CookieName
	, $CookieIDKey = self::CookieIDKey
	, $CookieHashKey = self::CookieHashKey
	, $CookieVHashKey = self::CookieVHashKey
	) {
		\GPCS::SESSION($SessionName, NULL);
		\GPCS::COOKIE($CookieName . "." . $CookieIDKey, NULL, NULL, '/');
		\GPCS::COOKIE($CookieName . "." . $CookieHashKey, NULL, NULL, '/');
		\GPCS::COOKIE($CookieName . "." . $CookieVHashKey, NULL, NULL, '/');
	}

	static function Crypt($str, $raw_output = false) {
		return T\Security::Crypt($str, $raw_output);
	}

}
