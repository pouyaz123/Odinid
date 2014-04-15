<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read string $drUser
 */
class Setting extends \Base\FormModel {

	const MaxUsernameChanges = 1;

	public function getPostName() {
		return "UserSetting";
	}

	public function getXSSPurification() {
		return false;
	}

	//----- attrs
	public $txtUsername;
	public $txtCurrentPassword;
	public $txtNewPassword;
	public $txtNewPasswordRepeat;
	public $chkBlockMatureContent = 1;
#
	public $UserID;
	private $_drUser = null;

	public function getdrUser() {
		return $this->_drUser;
	}

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('txtCurrentPassword', 'required'),
			array('txtUsername', 'match', 'pattern' => C\Regexp::Username),
			array('txtUsername', 'match', 'not' => true, 'pattern' => C\Regexp::Username_InvalidCases()),
			array('txtUsername', 'length', 'max' => $vl->Username['max']),
			array('txtUsername', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_users` WHERE `Username`=:val LIMIT 1'),
			array('txtNewPasswordRepeat', 'compare',
				'compareAttribute' => 'txtNewPassword'),
			array('chkBlockMatureContent', 'boolean'),
		);
	}

//	protected function beforeValidate() {
//	}

	public function attributeLabels() {
		return array(
			'txtUsername' => \t2::Site_User('Username'),
			'txtCurrentPassword' => \t2::Site_User('Current password'),
			'txtNewPassword' => \t2::Site_User('New password'),
			'txtNewPasswordRepeat' => \t2::Site_User('Confirm new password'),
			'chkBlockMatureContent' => \t2::Site_User('Block mature content'),
		);
	}

	public function Save() {
		if (!$this->validate())
			return false;
		$drUser = T\DB::GetRow("SELECT `UNChangeCount`, `Username`, IF(`Password`=:pw, 1, 0) AS CorrectPW"
						. " FROM `_users` WHERE `ID`=:id"
						, array(
					':id' => $this->UserID,
					':pw' => T\UserAuthenticate::Crypt($this->txtCurrentPassword)
						)
		);
		if (!$drUser['CorrectPW']) {
			$this->addError('txtCurrentPassword', \t2::Site_User('Invalid password'));
			return false;
		}
		$IsUNChange = ($this->txtUsername && $drUser['Username'] != $this->txtUsername && $drUser['UNChangeCount'] < self::MaxUsernameChanges);
		$Query = array();
		if ($IsUNChange || $this->txtNewPassword) {
			$Query[] = array(
				"UPDATE `_users` SET "
				. ($IsUNChange ? " `Username`=:un" : '')
				. ($this->txtNewPassword ? ", `Password`=:pw" : '')
				. " WHERE `ID`=:uid"
				, array(
					':un' => $this->txtUsername,
					':pw' => $this->txtNewPassword,
					':uid' => $this->UserID,
				)
			);
		}
		$Query[] = array(
			"UPDATE `_user_info` SET "
			. " `BlockMature`=:blk"
			. " WHERE `UID`=:uid"
			, array(
				':blk' => $this->chkBlockMatureContent,
				':uid' => $this->UserID,
			)
		);
		T\DB::Transaction($Query);
	}

	public function SetForm() {
		$dr = T\DB::GetRow("SELECT u.`Username`, ui.`BlockMature`, u.`UNChangeCount`"
						. " FROM `_users` u"
						. " INNER JOIN (SELECT 1) tmp ON u.`ID`=:uid"
						. " LEFT JOIN `_user_info` ui ON ui.`UID`=u.`ID`"
						, array(':uid' => $this->UserID));
		if ($dr) {
			$arrAttrs = array(
				'chkBlockMatureContent' => $dr['BlockMature'],
			);
			$this->attributes = $arrAttrs;
			$this->_drUser = $dr;
		}
	}

}
