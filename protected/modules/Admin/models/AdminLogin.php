<?php

namespace Admin\models;

use \Consts as C;
use \Tools as T;

/**
 * model for _users
 * NOTE : scenario name has been used as the post name in ->getPostName()
 * register, activation, login
 */
class AdminLogin extends \Base\FormModel {

	public function getPostName() {
		return $this->scenario;
	}

	protected $XSSPurification = false;

	const SessionName = 'adms';
	const CookieName = 'admc';
	const RememberHours = 8760; //365days

	public $txtUsername;
	public $txtPassword;
	public $chkRemember;
	public $txtCaptcha;

	protected function CleanViewStateOfSpecialAttrs() {
		$this->txtCaptcha = $this->txtPassword = null;
		parent::CleanViewStateOfSpecialAttrs();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('txtUsername, txtPassword, txtCaptcha', 'required',
				'on' => 'Login'),
			#
			array('chkRemember', 'boolean',
				'on' => 'Login'),
			array('txtCaptcha', '\Validators\Captcha',
				'on' => 'Login'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'txtUsername' => \Lng::Admin('tr_user', 'Username'),
			'txtPassword' => \Lng::Admin('tr_user', 'Password'),
			'chkRememberMe' => \Lng::Admin('tr_user', 'Remember me'),
			'txtCaptcha' => \Lng::Admin('tr_common', 'Captcha code'),
		);
	}

	function Login() {
		$drUser = null;
		if ($this->validate()) {
			$drUser = T\DB::GetRow(
							'SELECT `ID`, `Username`, `LastLoginIP`, `LastLoginTimeStamp`'
							. ' FROM `_admins`'
							. ' WHERE `Username`=:un AND `Status`=:active AND `Password`=:pw', array(
						':un' => $this->txtUsername,
						':pw' => md5($this->txtPassword),
						':active' => C\User::Status_Active,
			));
			if ($drUser) {
				$LoginIP = $_SERVER['REMOTE_ADDR'];
				$LoginTime = time();
				T\DB::Execute(
						'UPDATE `_admins`'
						. ' SET `LastLoginIP`=:ip, `LastLoginTimeStamp`=:time'
						. ' WHERE `ID`=:id', array(
					':id' => $drUser['ID'],
					':ip' => $LoginIP,
					':time' => $LoginTime,
				));
				self::MakeItLoggedIn($drUser, $this->chkRemember, $LoginIP, $LoginTime);
			} else
				$this->addError('', \Lng::Admin('tr_user', 'Invalid username or password'));
		}
		$this->CleanViewStateOfSpecialAttrs();
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
							'SELECT `ID`, `Username`, `LastLoginIP`, `LastLoginTimeStamp`'
							. ' FROM `_admins`'
							. ' WHERE `ID`=:id AND `Status`=:active', array(
						':id' => $ID,
						':active' => C\User::Status_Active,
			));

			if ($drUser) {
				//mytodo 3:REMOTE_ADDR OR LastLoginIP to validate user remember cookie
				$LoginIP = $_SERVER['REMOTE_ADDR'];
//				$LoginIP = $drUser['LastLoginIP'];
				$LoginTime = $drUser['LastLoginTimeStamp'];
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
