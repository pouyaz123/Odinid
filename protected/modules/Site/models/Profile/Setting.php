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

	public function getdrUser() {
		static $dr = null;
		if (!$dr) {
			$dr = T\DB::GetRow("SELECT u.`Username`, ui.`BlockMature`, u.`UNChangeCount`"
							. " FROM `_users` u"
							. " INNER JOIN (SELECT 1) tmp ON u.`ID`=:uid"
							. " LEFT JOIN `_user_info` ui ON ui.`UID`=u.`ID`"
							, array(':uid' => $this->UserID));
		}
		return $dr;
	}

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('txtCurrentPassword', 'required'),
			array('txtUsername', 'match', 'pattern' => C\Regexp::Username),
			array('txtUsername', 'match', 'not' => true, 'pattern' => C\Regexp::Username_InvalidCases()),
			array('txtUsername', 'length', 'max' => $vl->Username['max']),
			array('txtUsername', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_users` WHERE `Username`=:val AND `ID`!=:id LIMIT 1'
				, 'SQLParams' => array('id' => $this->UserID)),
			array('txtNewPasswordRepeat', 'compare',
				'compareAttribute' => 'txtNewPassword'),
			array('chkBlockMatureContent', 'boolean'),
		);
	}

//	protected function beforeValidate() {
//	}

	public function attributeLabels() {
		return array(
			'txtUsername' => \t2::site_site('Username'),
			'txtCurrentPassword' => \t2::site_site('Current password'),
			'txtNewPassword' => \t2::site_site('New password'),
			'txtNewPasswordRepeat' => \t2::site_site('Confirm new password'),
			'chkBlockMatureContent' => \t2::site_site('Block mature content'),
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
			$this->addError('txtCurrentPassword', \t2::site_site('Invalid password'));
			return false;
		}
		$IsUNChange = ($this->txtUsername && $drUser['Username'] != $this->txtUsername);
		if ($IsUNChange && $drUser['UNChangeCount'] >= self::MaxUsernameChanges) {
			$IsUNChange = false;
			$this->addError('txtUsername', \t2::site_site('You have reached the maximum'));
		}
		$Query = array();
		if ($IsUNChange || $this->txtNewPassword) {
			$Query[] = array(
				"UPDATE `_users` SET "
				. ($IsUNChange ? " `Username`=:un, UNChangeCount=UNChangeCount+1" : '')
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
		if (T\DB::Transaction($Query)) {
			if ($IsUNChange) {
				\Site\models\User\Login::SetSessionDR('Username', $this->txtUsername);
				T\HTTP::Redirect_Immediately($_SERVER['REQUEST_URI']);
			}
		}
	}

	public function SetForm() {
		$dr = $this->getdrUser();
		if ($dr) {
			$arrAttrs = array(
				'chkBlockMatureContent' => $dr['BlockMature'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
