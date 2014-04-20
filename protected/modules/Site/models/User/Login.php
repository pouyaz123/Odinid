<?php

namespace Site\models\User;

use \Consts as C;
use \Tools as T;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @method boolean|array Login() false or user datarow : callLogin + events
 */
class Login extends \Base\FormModel {

	public function getPostName() {
		return "Login";
	}

	public function getXSSPurification() {
		return false;
	}

	const SessionName = C\SessCookNames::UserLogin_SessionName;
	const CookieName = C\SessCookNames::UserLogin_CookieName;
	const RememberHours = 168; //7days

	public $txtUsername;
	public $txtPassword;
	public $chkRemember = false;
	public $txtCaptcha;

	protected function CleanViewStateOfSpecialAttrs() {
		$this->txtCaptcha = $this->txtPassword = null;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('txtUsername, txtPassword, txtCaptcha', 'required'),
			#
			array('chkRemember', 'boolean'),
			array('txtCaptcha', 'MyCaptcha'),
		);
	}

	public function attributeLabels() {
		return array(
			'txtUsername' => \t2::site_site('Username'),
			'txtPassword' => \t2::site_site('Password'),
			'chkRemember' => \t2::site_site('Remember me'),
			'txtCaptcha' => \t2::General('Captcha code'),
		);
	}

	function onAfterLogin() {
		$this->CleanViewStateOfSpecialAttrs();
	}

	function callLogin() {
		$IsTrue = false;
		if ($this->validate()) {
			$drUser = T\DB::GetRow(
							'SELECT `ID`, `Username`, `LastLoginIP`, `LastLoginTStamp`, `Status`'
							. ' FROM `_users`'
							. ' WHERE `Username`=:un AND `Password`=:pw', array(
						':un' => $this->txtUsername,
						':pw' => T\UserAuthenticate::Crypt($this->txtPassword),
			));
			$IsTrue = $drUser && $drUser['Status'] === C\User::Status_Active;
			if ($IsTrue) {
				$LoginIP = $_SERVER['REMOTE_ADDR'];
				$LoginTime = time();
				T\DB::Execute(
						'UPDATE `_users`'
						. ' SET `LastLoginIP`=:ip, `LastLoginTStamp`=:time'
						. ' WHERE `ID`=:id', array(
					':id' => $drUser['ID'],
					':ip' => $LoginIP,
					':time' => $LoginTime,
				));
				self::MakeItLoggedIn($drUser, $this->chkRemember, $LoginIP, $LoginTime);
			} elseif (!$drUser)
				$this->addError('', \t2::site_site('Invalid username or password'));
			elseif ($drUser['Status'] !== C\User::Status_Active)
				$this->addError('', \t2::site_site('Plz activate your account first'));
		}
		return $IsTrue;
	}

	private static function MakeItLoggedIn($drUser, $boolRemember, $LoginIP, $LoginTime) {
		T\UserAuthenticate::MakeItLoggedIn(
				self::SessionName
				, $drUser
				, $boolRemember
				, self::CookieName
				, $drUser['ID']
				, $drUser['Username']
				, $LoginIP
				, $LoginTime
				, self::RememberHours
				, \Conf::SSLOn_Site
		);
	}

	/**
	 * @return boolean
	 */
	public static function IsLoggedIn() {
		$drUser = self::GetSessionDR();
		if ($drUser)
			return true;
//		if ($drUser = \GPCS::SESSION($SessionName)) {
//			$RefreshKW = self::GetRefreshCacheKW($SessionName, $drUser['ID']);
//			if (($Task = \F3::get($RefreshKW))) {
//				\F3::clear($RefreshKW);
//				if ($Task === 'LOGOUT') {
//					self::Logout_Base($SessionName, $CookieName);
//					return false;
//				} elseif ($Task === 'REFRESH')
//					static::RefreshDR();
//			}
//			return true;
//		}

		$CookieName = self::CookieName;
		$ID = \GPCS::COOKIE("$CookieName." . T\UserAuthenticate::CookieIDKey);
		$Hash = \GPCS::COOKIE("$CookieName." . T\UserAuthenticate::CookieHashKey);
		if (isset($ID) && $Hash && T\UserAuthenticate::IsValidHash($CookieName, $ID)) {
			$drUser = T\DB::GetRow(
							'SELECT `ID`, `Username`, `LastLoginIP`, `LastLoginTStamp`'
							. ' FROM `_users`'
							. ' WHERE `ID`=:id AND `Status`=:active', array(
						':id' => $ID,
						':active' => C\User::Status_Active,
			));

			if ($drUser) {
				//mytodo 3:REMOTE_ADDR OR LastLoginIP to validate user remember cookie (more security)
				$LoginIP = $_SERVER['REMOTE_ADDR'];
//				$LoginIP = $drUser['LastLoginIP'];
				$LoginTime = $drUser['LastLoginTStamp'];
				if ($Hash === T\UserAuthenticate::GetHash($drUser['Username'], $LoginTime, $LoginIP)) {
					self::MakeItLoggedIn($drUser, false, $LoginTime, $LoginIP);
					return true;
				}
			}
			self::Logout();
			return FALSE;
		}
		self::Logout(); //to clear every thing
		return FALSE;
	}

	public static function GetSessionDR($Field = NULL) {
		static $dr = NULL;
		if (!$dr)
			$dr = \GPCS::SESSION(self::SessionName);
		if (!$dr)
			return NULL;
		return $Field ? (isset($dr[$Field]) ? $dr[$Field] : NULL) : $dr;
	}

	public static function SetSessionDR($Field, $Value) {
		$_SESSION[self::SessionName][$Field] = $Value;
	}

	public static function Logout() {
		T\UserAuthenticate::Logout(self::SessionName, self::CookieName);
	}

}
