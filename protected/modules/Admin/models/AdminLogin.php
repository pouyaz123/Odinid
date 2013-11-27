<?php

namespace Admin\models;

use \Consts as C;
use \Tools as T;

/**
 * model for _users
 * NOTE : scenario name has been used as the post name in ->PostName()
 * register, activation, login
 */
class AdminLogin extends \Base\FormModel {

	public function PostName() {
		return $this->scenario;
	}

	const SessionName = 'adms';
	const CookieName = 'admc';
	const RememberHours = 8760; //365days

	public $txtUsername;
	public $txtPassword;
	public $chkRemember;
	public $txtCaptcha;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('txtUsername, txtPassword, txtCaptcha', 'required',
				'on' => 'Login'),
			#
//			array('txtPassword', 'length',
//				'min' => C\Regexp::Password_MinLength,
//				'on' => 'Login'),
			#
			array('chkRemember', 'boolean',
				'on' => 'Login'),
			array('txtCaptcha', 'captcha',
				'on' => 'Login'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'txtUsername' => \Lng::Admin('User', 'Username'),
			'txtPassword' => \Lng::Admin('User', 'Password'),
			'chkRememberMe' => \Lng::Admin('User', 'Remember me'),
			'txtCaptcha' => \Lng::Admin('User', 'Verification code'),
		);
	}

	function Login() {
		if ($this->validate()) {
			$drUser = T\DB::GetRow('
				SELECT `ID`, `Username`, `LastLoginIP`, `LastLoginTimeStamp`
				FROM `_admins`
				WHERE `Username`=:un AND `Status`=:active AND `Password`=:pw', array(
						':un' => $this->txtUsername,
						':pw' => md5($this->txtPassword),
						':active' => C\User::Status_Active,
					));
			if ($drUser) {
				$LoginIP = $_SERVER['REMOTE_ADDR'];
				$LoginTime = time();
				T\DB::Execute('
					UPDATE `_admins`
					SET `LastLoginIP`=:ip, `LastLoginTimeStamp`=:time
					WHERE `ID`=:id', array(
					':id' => $drUser['ID'],
					':ip' => $LoginIP,
					':time' => $LoginTime,
				));
				self::MakeItLoggedIn($drUser, $this->chkRemember, $LoginIP, $LoginTime);
				return true;
			} else {
				$this->addError('', \Lng::Admin('User', 'Invalid username or password'));
				return false;
			}
		}
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

	public static function IsLoggedIn() {
		$drUser = \GPCS::SESSION(self::SessionName);
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
		if ($ID && $Hash && T\UserAuthenticate::IsValidHash($CookieName, $ID)) {
			$drUser = T\DB::GetRow('
				SELECT `ID`, `Username`, `LastLoginIP`, `LastLoginTimeStamp`
				FROM `_admins`
				WHERE `ID`=:id AND `Status`=:active', array(
						':id' => $ID,
						':active' => C\User::Status_Active,
					));

			if ($drUser) {
				//TODO1:REMOTE_ADDR OR LastLoginIP to validate user remember cookie
				$LoginIP = $_SERVER['REMOTE_ADDR'];
//				$LoginIP = $drUser['LastLoginIP'];
				$LoginTime = $drUser['LastLoginTimeStamp'];
				if ($Hash === T\UserAuthenticate::GetHash($drUser['Username'], $LoginTime, $LoginIP)) {
					static::MakeItLoggedIn($drUser, false, $LoginTime, $LoginIP);
					return true;
				}
			}
			self::Logout();
			return FALSE;
		}
		self::Logout(); //to clear every thing
		return FALSE;
	}

	public static function Logout() {
		T\UserAuthenticate::Logout(self::SessionName, self::CookieName);
	}

}
