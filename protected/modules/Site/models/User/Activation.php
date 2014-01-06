<?php

namespace Site\models\User;

use \Consts as C;
use \Tools as T;

/**
 * Tasks :
 * verifying emails (on changing primary email)
 * user activation (new registered users)
 * resend activation code to refresh expired activation links
 */
class Activation extends \Base\FormModel {

	public function getPostName() {
		return $this->scenario;
	}

	//----attrs
	//activation
	public $txtActivationCode;
	//resend activation link
	public $txtEmail;
	public $txtEmailRepeat;
	public $txtCaptcha;
	#
	private $_drActivationRow = null;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			#activation
			array('txtActivationCode', 'required',
				'on' => 'Activation'),
			array('txtActivationCode', 'IsValidActivationCode',
				'on' => 'Activation'),
			#resend activation
			array('txtEmail, txtEmailRepeat, txtCaptcha', 'required',
				'on' => 'ResendActivation'),
			#
			array('txtEmail', 'email',
				'on' => 'ResendActivation'),
			array('txtEmailRepeat', 'compare',
				'compareAttribute' => 'txtEmail',
				'on' => 'ResendActivation'),
			array('txtEmail', 'IsExist', //is a not verified, not activated email
				'SQL' => "SELECT COUNT(*) FROM `_user_recoveries` ur"
				. " INNER JOIN (SELECT 1) tmp ON ur.`Type`=:activation AND ur.`PendingEmail`=:val"
				. " INNER JOIN `_users` u ON u.`ID`=ur.`UID` AND u.`Status`!=:disabled" //prevent blocked users
				. " LIMIT 1",
				'SQLParams' => array(
					':disabled' => C\User::Status_Disabled,
					':activation' => C\User::Recovery_Activation,
				),
				'on' => 'ResendActivation'),
			#
			array('txtCaptcha', 'MyCaptcha',
				'on' => 'ResendActivation'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'txtActivationCode' => \t2::Site_User('Activation code'),
			'txtEmail' => \t2::Site_User('Email'),
			'txtEmailRepeat' => \t2::Site_User('Confirm email'),
			'txtCaptcha' => \t2::General('Captcha code'),
		);
	}

	function IsValidActivationCode() {
		$this->_drActivationRow = T\DB::GetRow(
						"SELECT `UID`, `PendingEmail` AS Email, `CompanyDomain`, `Code`"
						. " FROM `_user_recoveries`"
						. " WHERE `Code`=:code AND (`Type`=:activation OR `Type`=:emailverify)"
						. " AND `TimeStamp`>" . (time() - (T\Settings::GetValue('ActivationLink_LifeTime') * 60 * 60))
						, array(
					':code' => $this->txtActivationCode,
					':activation' => C\User::Recovery_Activation,
					':emailverify' => C\User::Recovery_EmailVerify,
						)
		);

		//Garbage collection
		T\GCC::UserRecoveries();

		if (!$this->_drActivationRow)
			$this->addError('txtActivationCode', \t2::Site_User('Invalid activation code'));
	}

	function Activate() {
		if (!$this->validate())
			return false;

		$drAR = &$this->_drActivationRow;
		$CommonParams = array(
			':uid' => $drAR['UID'],
		);
		$Queries = array();
		$Queries[] = array("UPDATE `_users` SET `Status`=:active, `PrimaryEmail`=:email"
			. " WHERE `ID`=:uid AND `Status`!=:disabled"
			, array(
				':active' => C\User::Status_Active,
				':disabled' => C\User::Status_Disabled, //to prevent reactivating of blocked users
				':email' => $drAR['Email'],
			)
		);
		if ($drAR['CompanyDomain']) {
			$Queries[] = array("UPDATE `_company_info` SET `Domain`=:domain"
				. " WHERE `OwnerUID`=:uid"
				, array(':domain' => $drAR['CompanyDomain']));
			//no double company domain
			$Queries[] = array("UPDATE `_user_recoveries` SET `CompanyDomain`=NULL WHERE `CompanyDomain`=:domain"
				, array(':domain' => $drAR['CompanyDomain']));
		}
		$Queries[] = array("DELETE FROM `_user_recoveries` WHERE `Code`=:code"
			, array(':code' => $this->txtActivationCode));
		$Result = T\DB::Transaction($Queries, $CommonParams, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::Site_User('Activation failed!'));
				});
		return $Result ? true : false;
	}

	function ResendActivationLink() {
		if (!$this->validate())
			return false;

		$Username = T\DB::GetField("SELECT u.`Username` FROM `_user_recoveries` ur"
						. " INNER JOIN (SELECT 1) tmp ON ur.`PendingEmail`=:email"
						. " INNER JOIN `_users` u ON u.`ID`=ur.`UID`"
						, array(':email' => $this->txtEmail)
		);
		$Code = T\DB::GetUniqueCode('_user_recoveries', 'Code');
		$Result = T\DB::Execute("UPDATE `_user_recoveries` SET `Code`=:code, `TimeStamp`=:time WHERE `PendingEmail`=:email"
						, array(
					':code' => $Code,
					':time' => time(),
					':email' => $this->txtEmail,
						)
		);
		if (!$Result) {
			\html::ErrMsg_Exit(\t2::Site_Common('Failed! Plz retry.'));
			return FALSE;
		} else {
			self::SendActivationEmail($Code, $this->txtEmail, $Username);
			return TRUE;
		}
	}

	static function SendActivationEmail($ActivationCode, $Email, $Name = '') {
		$MS = T\SendMail::GetConfiguredMailSender();
		$MS->AddAddress($Email, $Name);
		if (!$MS->Send2(
						\t2::Site_User('Activation link')
						, T\SendMail::GetEmailTemplate('activation', null, array(
							'Code' => $ActivationCode,
							'CodeUrl' => \Yii::app()->createAbsoluteUrl(T\HTTP::URL_InsertGetParams(\Site\Consts\Routes::UserActivation, "code=$ActivationCode")),
							'Url' => \Yii::app()->createAbsoluteUrl(\Site\Consts\Routes::UserActivation),
							'Name' => $Name,
							'Email' => $Email,
						))
				)) {
			\html::ErrMsg_Exit(\t2::Site_User('Failed to send activation link!'));
			\Err::TraceMsg_Method(__METHOD__, "Failed to send the user activation link", func_get_args());
		}
	}

}
