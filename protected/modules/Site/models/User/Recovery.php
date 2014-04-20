<?php

namespace Site\models\User;

use \Consts as C;
use \Tools as T;

/**
 * Tasks :
 * <ul>
 * <li>send recovery link emails</li>
 * <li>user recovery based on the recovery code</li>
 * </ul>
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @method boolean Recover()	//callRecover + events
 * @method boolean SendRecoveryLink()	//callSendRecoveryLink + events
 */
class Recovery extends \Base\FormModel {

	public function getPostName() {
		return $this->scenario;
	}

	public function getXSSPurification() {
		return false;
	}

	//----attrs
	public $txtCaptcha;
	//recovery
	public $txtRecoveryCode;
	public $txtNewPassword;
	public $txtNewPasswordRepeat;
	//send recovery link
	public $txtEmail;
	public $txtEmailRepeat;
	#
	private $_drRecoveryRow = null;

	protected function CleanViewStateOfSpecialAttrs() {
		$this->txtCaptcha = $this->txtNewPassword = $this->txtNewPasswordRepeat = null;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			#recovery
			array('txtRecoveryCode, txtNewPassword, txtNewPasswordRepeat', 'required',
				'on' => 'Recovery'),
			//validate recovery code by hand only when captcha was right in the post to defeat robots
//			array('txtRecoveryCode', 'IsValidRecoveryCode',
//				'on' => 'Recovery'),
			array_merge(array('txtNewPassword', 'length'), \ValidationLimits\User::GetInstance()->Password),
			array('txtNewPasswordRepeat', 'compare',
				'compareAttribute' => 'txtNewPassword',
				'on' => 'Recovery'),
			#
			#resend recovery link
			array('txtEmail, txtEmailRepeat', 'required',
				'on' => 'SendRecoveryLink'),
			array('txtEmail', 'email',
				'on' => 'SendRecoveryLink'),
			array('txtEmailRepeat', 'compare',
				'compareAttribute' => 'txtEmail',
				'on' => 'SendRecoveryLink'),
			array('txtEmail', 'IsExist',
				'SQL' => "SELECT COUNT(*) FROM `_users` WHERE `PrimaryEmail`=:val AND `Status`=:active LIMIT 1",
				'SQLParams' => array(
					':active' => C\User::Status_Active,
				),
				'on' => 'SendRecoveryLink'),
			#
			array('txtCaptcha', 'required'),
			array('txtCaptcha', 'MyCaptcha'),
		);
	}

	public function attributeLabels() {
		return array(
			'txtRecoveryCode' => \t2::site_site('Recovery code'),
			'txtNewPassword' => \t2::site_site('New password'),
			'txtNewPasswordRepeat' => \t2::site_site('Confirm new password'),
			'txtEmail' => \t2::site_site('Email'),
			'txtEmailRepeat' => \t2::site_site('Confirm email'),
			'txtCaptcha' => \t2::General('Captcha code'),
		);
	}

	function IsValidRecoveryCode() {
		$this->_drRecoveryRow = T\DB::GetRow(
						"SELECT `UID`, `Code` FROM `_user_recoveries`"
						. " WHERE `Code`=:code AND `Type`=:recovery"
						. " AND `TimeStamp`>" . (time() - (T\Settings::GetValue('RecoveryLink_LifeTime') * 60 * 60))
						, array(
					':code' => $this->txtRecoveryCode,
					':recovery' => C\User::Recovery_Recovery,
						)
		);

		//Garbage collection
		T\GCC::UserRecoveries();

		if (!$this->_drRecoveryRow) {
			$this->addError('txtRecoveryCode', \t2::site_site('Invalid recovery code'));
			return false;
		}
		return true;
	}

	function onAfterSendRecoveryLink() {
		$this->CleanViewStateOfSpecialAttrs();
	}

	/**
	 * use ->SendRecoveryLink() instead to trigger relative important events
	 * @return boolean
	 */
	function callSendRecoveryLink() {
		if (!$this->validate())
			return false;

		$drUser = T\DB::GetRow("SELECT u.`Username`, u.`ID`, ur.`Code` AS AnyRecovery"
						. " FROM `_users` u"
						. " INNER JOIN (SELECT 1) tmp ON u.`PrimaryEmail`=:email"
						. " LEFT JOIN `_user_recoveries` ur ON ur.`UID`=u.`ID` AND `Type`=:recovery"
						, array(
					':email' => $this->txtEmail,
					':recovery' => C\User::Recovery_Recovery
						)
		);
		$Code = T\DB::GetUniqueCode('_user_recoveries', 'Code');
		$Result = T\DB::Execute(
						(!$drUser['AnyRecovery'] ?
								"INSERT INTO `_user_recoveries`(`UID`, `Code`, `TimeStamp`, `Type`)"
								. " VALUES(:uid, :code, :time, :recovery)" :
								"UPDATE `_user_recoveries` SET `Code`=:code, `TimeStamp`=:time"
								. " WHERE `UID`=:uid AND `Type`=:recovery"
						)
						, array(
					':uid' => $drUser['ID'],
					':code' => $Code,
					':time' => time(),
					':recovery' => C\User::Recovery_Recovery,
						)
		);
		if (!$Result) {
			\html::ErrMsg_Exit(\t2::site_site('Failed! Plz retry.'));
			return FALSE;
		} else {
			self::SendRecoveryEmail($Code, $this->txtEmail, $drUser['Username']);
			return TRUE;
		}
	}

	function onAfterRecover() {
		$this->CleanViewStateOfSpecialAttrs();
	}

	/**
	 * use ->Recover() instead to trigger relative important events
	 * @return boolean
	 */
	function callRecover() {
		if (!$this->validate())
			return false;
		//validate recovery code by hand only when captcha was right in the post to defeat robots
		if (!$this->IsValidRecoveryCode())
			return false;

		$drRR = &$this->_drRecoveryRow;
		$CommonParams = array(
			':uid' => $drRR['UID'],
		);
		$Queries = array();
		$Queries[] = array("UPDATE `_users` SET `Password`=:pw"
			. " WHERE `ID`=:uid AND `Status`!=:disabled"
			, array(
				':disabled' => C\User::Status_Disabled, //to prevent blocked users
				':pw' => T\UserAuthenticate::Crypt($this->txtNewPassword),
			)
		);
		$Queries[] = array("DELETE FROM `_user_recoveries` WHERE `Code`=:code"
			, array(':code' => $this->txtRecoveryCode));
		$Result = T\DB::Transaction($Queries, $CommonParams, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::site_site('Recovery failed!'));
				});
		return $Result ? true : false;
	}

	static function SendRecoveryEmail($RecoveryCode, $Email, $Name = '') {
		$MS = T\SendMail::GetConfiguredMailSender();
		$MS->AddAddress($Email, $Name);
		if (!$MS->Send2(
						\t2::site_site('Recovery link')
						, T\SendMail::GetEmailTemplate('recovery', null, array(
							'Code' => $RecoveryCode,
							'CodeUrl' => \Yii::app()->createAbsoluteUrl(T\HTTP::URL_InsertGetParams(\Site\Consts\Routes::UserRecoveryCode, "code=$RecoveryCode")),
							'Url' => \Yii::app()->createAbsoluteUrl(\Site\Consts\Routes::UserRecoveryCode),
							'Name' => $Name,
							'Email' => $Email,
						))
				)) {
			\Err::TraceMsg_Method(__METHOD__, "Failed to send the user recovery link", func_get_args());
			\html::ErrMsg_Exit(\t2::site_site('Failed to send recovery link!'));
		}
	}

}
