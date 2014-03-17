<?php

namespace Admin\models;

use \Consts as C;
use \Tools as T;

/**
 * @method boolean|array Login() callLogin + events
 */
class AdminLogin extends \Base\FormModel {

	public function getPostName() {
		return 'Login';
	}

	public function getXSSPurification() {
		return false;
	}

	const SessionName = C\SessCookNames::AdminLogin_SessionName;
	const CookieName = C\SessCookNames::AdminLogin_CookieName;
	const RememberHours = 8760; //365days

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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'txtUsername' => \t2::Admin_User('Username'),
			'txtPassword' => \t2::Admin_User('Password'),
			'chkRemember' => \t2::Admin_User('Remember me'),
			'txtCaptcha' => \t2::General('Captcha code'),
		);
	}

	function onAfterLogin() {
		$this->CleanViewStateOfSpecialAttrs();
	}

	function callLogin() {
		$drUser = null;
		if ($this->validate()) {
			$drUser = T\DB::GetRow(
							'SELECT `ID`, `Username`, `LastLoginIP`, `LastLoginTStamp`'
							. ' FROM `_admins`'
							. ' WHERE `Username`=:un AND `Status`=:active AND `Password`=:pw', array(
						':un' => $this->txtUsername,
						':pw' => T\UserAuthenticate::Crypt($this->txtPassword),
						':active' => C\User::Status_Active,
			));
			if ($drUser) {
				$LoginIP = $_SERVER['REMOTE_ADDR'];
				$LoginTime = time();
				T\DB::Execute(
						'UPDATE `_admins`'
						. ' SET `LastLoginIP`=:ip, `LastLoginTStamp`=:time'
						. ' WHERE `ID`=:id', array(
					':id' => $drUser['ID'],
					':ip' => $LoginIP,
					':time' => $LoginTime,
				));
				self::MakeItLoggedIn($drUser, $this->chkRemember, $LoginIP, $LoginTime);
			} else
				$this->addError('', \t2::Admin_User('Invalid username or password'));
		}
		return $drUser? : false;
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
				, \Conf::SSLOn_Admin
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
							. ' FROM `_admins`'
							. ' WHERE `ID`=:id AND `Status`=:active', array(
						':id' => $ID,
						':active' => C\User::Status_Active,
			));

			if ($drUser) {
				//mytodo 3:REMOTE_ADDR OR LastLoginIP to validate admin user remember cookie (more security)
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

	public static function Logout() {
		T\UserAuthenticate::Logout(self::SessionName, self::CookieName);
	}

}
