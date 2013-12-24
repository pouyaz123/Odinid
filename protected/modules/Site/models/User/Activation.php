<?php

namespace Site\models\User;

use \Consts as C;
use \Tools as T;

class Activation extends \Base\FormModel {

	public function getPostName() {
		return 'Activation';
	}

	//attrs
	public $txtActivationCode;

//	public function getArrAccountTypes() {
//		return $this->_arrAccountTypes;
//	}
//
//	protected function CleanViewStateOfSpecialAttrs() {
//		$this->txtPassword = $this->txtCaptcha = null;
//		parent::CleanViewStateOfSpecialAttrs();
//	}
//
//	/**
//	 * @return array validation rules for model attributes.
//	 */
//	public function rules() {
//		return array(
//			#common
//			array('txtEmail, txtUsername, txtPassword, txtCaptcha', 'required'),
//			array('ddlAccountType', 'in', 'range' => array_keys($this->_arrAccountTypes)),
//			array('txtPassword', 'length',
//				'min' => C\Regexp::Password_MinLength),
//			array('txtCaptcha', '\Validators\Captcha'),
//			#email
//			array('txtEmail', 'email'),
//			array('txtEmailRepeat', 'compare',
//				'compareAttribute' => 'txtEmail'),
//			array('txtEmail', 'IsUnique',
//				'SQL' => 'SELECT COUNT(*) FROM `_user_contactbook` WHERE `Email`=:val LIMIT 1',
//				'Msg' => '{attribute} "{value}" has been used previously.'),
//			#username
//			array('txtUsername', 'match', 'pattern' => C\Regexp::Username),
//			array('txtUsername', 'match', 'not'=>true, 'pattern' => C\Regexp::Username_InvalidCases),
//			array('txtUsername', 'length',
//				'min' => C\Regexp::Username_MinLen, 'max' => C\Regexp::Username_MaxLen),
//			array('txtUsername', 'IsUnique',
//				'SQL' => 'SELECT COUNT(*) FROM `_users` WHERE `Username`=:val LIMIT 1',
//				'Msg' => '{attribute} "{value}" has been used previously.'),
//			#artist
//			array('txtInvitationCode', 'required',
//				'on' => 'ArtistRegister'),
//			array('txtInvitationCode', 'IsValidInvitation',
//				'on' => 'ArtistRegister'),
//			#company
//			array('ddlCountry, ddlDivision, ddlCity, txtCountry, txtDivision, txtCity', 'required', //, txtAddress1, txtAddress2
//				'on' => 'CompanyRegister'),
//			array('txtCompanyURL', 'url',
//				'on' => 'CompanyRegister'),
//			array('txtCompanyURL', 'IsClaimedCompanyDomain',
//				'on' => 'CompanyRegister'),
//		);
//	}
//
//	/**
//	 * @return array customized attribute labels (name=>label)
//	 */
//	public function attributeLabels() {
//		return array(
//			'ddlAccountType' => \Lng::Site('tr_user', 'Account type'),
//			'txtEmail' => \Lng::Site('tr_user', 'Email'),
//			'txtEmailRepeat' => \Lng::Site('tr_user', 'Confirm email'),
//			'txtUsername' => \Lng::Site('tr_user', 'Username'),
//			'txtPassword' => \Lng::Site('tr_user', 'Password'),
//			'txtCaptcha' => \Lng::Site('tr_common', 'Captcha code'),
//			#artist
//			'txtInvitationCode' => \Lng::Site('tr_user', 'Invitation code'),
//			#Company
//			'txtCompanyURL' => \Lng::Site('tr_company', 'Company web URL'),
//			#location
//			'ddlCountry' => \Lng::Site('tr_common', 'Country'),
//			'ddlDivision' => \Lng::Site('tr_common', 'Division'),
//			'ddlCity' => \Lng::Site('tr_common', 'City'),
//			'txtCountry' => \Lng::Site('tr_common', 'Country'),
//			'txtDivision' => \Lng::Site('tr_common', 'Division'),
//			'txtCity' => \Lng::Site('tr_common', 'City'),
////			'txtAddress1' => \Lng::Site('tr_common', 'Address 1'),
////			'txtAddress2' => \Lng::Site('tr_common', 'Address 2'),
//		);
//	}
//
//	function IsValidInvitation() {
//		$this->_drUserType = T\DB::GetRow(
//						"SELECT `UserTypeID`, `UserTypeExpDate`"
//						. " FROM `_user_invitations`"
//						. " WHERE `Code`=:code"
//						. " AND (ISNULL(`InvitationExpDate`) OR `InvitationExpDate`='' OR `InvitationExpDate`>NOW())"
//						, array(':code' => $this->txtInvitationCode));
//		if (!$this->_drUserType)
//			$this->addError('txtInvitationCode', \Lng::Site('tr_user', 'Invalid invitation code'));
//	}

	static function SendActivationEmail($ActivationCode, $Email, $Name = '') {
		\Err::DebugBreakPoint(\Yii::app()->controller->render('Site.views.emails.activation', array(
					'Code' => $ActivationCode,
					'Url' => \Yii::app()->createUrl(\Site\Consts\Routes::UserActivation, array(), array('code' => $ActivationCode))
				)));
		$MS = T\SendMail::GetConfiguredMailSender();
		$MS->AddAddress($Email, $Name);
		$MS->send2(
				\Lng::Site('tr_user', 'Activation link')
				, \Yii::app()->controller->render('Site.views.emails.activation', array(
					'Code' => $ActivationCode,
					'Url' => \Yii::app()->createUrl(\Site\Consts\Routes::UserActivation, array(), array('code' => $ActivationCode))
				))
		);
	}

}
