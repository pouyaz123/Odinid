<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-write event $onSave push on save event handlers here
 * @property-read array $dtEmails
 * @property-read array $dtFreshEmails
 * @property-read boolean $IsPrimaryEmailEdit
 * @property-read string $ActivationEmail
 * @property-read string $ActivationCode
 * @property-read string $PendingEmail
 * @property Info $owner
 */
class Info_Emails extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'hdnEmailID, chkIsPrivate',
		));
	}

	public $hdnEmailID;
	public $txtEmail;
	public $chkIsPrivate = true;
	private $_PendingEmail;

	public function getPendingEmail() {
		return $this->_PendingEmail;
	}

	private $_IsPrimaryEmailEdit = false;

	public function getIsPrimaryEmailEdit() {
		return $this->_IsPrimaryEmailEdit;
	}

	private $_ActivationEmail = null;

	public function getActivationEmail() {
		return $this->_ActivationEmail;
	}

	private $_ActivationCode = null;

	function getActivationCode() {
		return $this->_ActivationCode;
	}

	public function onBeforeRules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		$e->params['arrRules'] = array_merge($e->params['arrRules'], array(
			array('hdnEmailID', 'required',
				'on' => 'Edit, Delete, ResetActivationLink, SetAsPrimary'),
			array('hdnEmailID', 'IsExist', //only to resend activation link
				'SQL' => 'SELECT COUNT(*) FROM `_user_emails` WHERE `CombinedID`=:val AND `UID`=:uid AND NOT ISNULL(`PendingEmail`)',
				'SQLParams' => array(':uid' => $this->owner->drUser->ID),
				'on' => 'ResetActivationLink'),
			array('hdnEmailID', 'IsExist', //making primary (must not be pending)
				'SQL' => 'SELECT COUNT(*) FROM `_user_emails` WHERE `CombinedID`=:val AND `UID`=:uid AND ISNULL(`PendingEmail`)',
				'SQLParams' => array(':uid' => $this->owner->drUser->ID),
				'on' => 'SetAsPrimary'),
			array('hdnEmailID', 'IsExist', //edit delete at all
				'SQL' => 'SELECT COUNT(*) FROM `_user_emails` WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->owner->drUser->ID),
				'on' => 'Edit, Delete'),
			array('txtEmail', 'required',
				'on' => 'Add, Edit'),
			array_merge(array('txtEmail', 'length',
				'on' => 'Add, Edit'), $vl->Email),
			array('txtEmail', 'email',
				'on' => 'Add, Edit'),
			array('chkIsPrivate', 'boolean',
				'on' => 'Add, Edit'),
		));
	}

	public function beforeValidate($event) {
		$owner = $this->owner;
		$unq = new \Validators\DBNotExist();
		$unq->attributes = array('txtEmail');
		$unq->SQL = 'SELECT COUNT(*) FROM `_user_emails` WHERE '
				. ($owner->scenario == 'Edit' || $this->hdnEmailID ? ' `CombinedID`!=:combid AND ' : '')
				. ' (`Email`=:val OR (`PendingEmail`=:val AND `UID`=:uid))';
		$unq->SQLParams = array(
			':combid' => $this->hdnEmailID,
			':uid' => $owner->drUser['ID']
		);
		$unq->except = 'Delete';
		$owner->validatorList->add($unq);
	}

	public function afterValidate(\CEvent $event) {
		if ($this->owner->scenario != 'Delete')
			$this->ValidateNewEmail();
	}

	/**
	 * Validates the maximum numbre of contacts
	 */
	private function ValidateNewEmail() {
		$owner = $this->owner;
		if (!$this->hdnEmailID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_user_emails` WHERE `UID`=:uid"
							, array(':uid' => $owner->drUser['ID']));
			if ($Count && $Count >= T\Settings::GetValue('MaxUserContacts'))
				$owner->addError('', \t2::site_site('You have reached the maximum'));
		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'txtEmail' => \t2::site_site('Email'),
			'chkIsPrivate' => \t2::site_site('Private'),
		));
	}

	public function getdtEmails($ID = NULL, $refresh = false) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			$arrDTs[$StaticIndex] = T\DB::GetTable("SELECT *"
							. " FROM `_user_emails`"
							. " WHERE " . ($ID ? " CombinedID=:id AND " : "") . " `UID`=:uid"
							. " ORDER BY `OrderNumber`"
							, array(
						':uid' => $this->owner->drUser->ID,
						':id' => $ID,
							)
			);
		}
		return $arrDTs[$StaticIndex]? : array();
	}

	/**
	 * gets fresh data table only once after the edit or delete process
	 * @param string $ID
	 * @return array
	 */
	public function getdtFreshEmails($ID = null) {
		static $F = true;
		$R = $this->getdtEmails($ID, $F);
		$F = false;
		return $R;
	}

	public function onDelete(\CEvent $e) {
		$this->raiseEvent('onDelete', $e);
		$owner = $this->owner;
		$owner->addTransactions(array(
			array(
				"DELETE FROM `_user_emails` WHERE `CombinedID`=:id AND ISNULL(`IsPrimary`)",
				array(':id' => $this->hdnEmailID)
			),
			array(
				"DELETE FROM `_user_recoveries` WHERE `EmailID`=:id",
				array(':id' => $this->hdnEmailID)
			)
		));
	}

	/**
	 * remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	 * @param \CEvent $e
	 * @return boolean
	 */
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		if (!$this->txtEmail || $owner->scenario == 'Delete') {
			if ($owner->scenario == 'Edit')
				$owner->Delete();
			return false;
		}
		$arrTrans = array();
		$drEditingEmail = null;
		if ($owner->scenario == 'Edit') {
			$drEditingEmail = T\DB::GetRow("SELECT `Email`, `PendingEmail` FROM `_user_emails` WHERE `CombinedID`=:combid"
							, array(
						':combid' => $this->hdnEmailID
							)
			);
			if ($drEditingEmail['PendingEmail'] && (!$this->txtEmail || $drEditingEmail['Email'] == $this->txtEmail)) {
				$arrTrans[] = array(
					"DELETE FROM `_user_recoveries` WHERE `EmailID`=:id",
					array(':id' => $this->hdnEmailID)
				);
			}
		}
		if (!$this->hdnEmailID)
			$CombinedID = T\DB::GetNewID_Combined(
							'_user_emails'
							, 'CombinedID'
							, 'UID=:uid'
							, array(':uid' => $owner->drUser->ID)
							, array(
						'ReturnTheQuery' => false,
						'PrefixQuery' => "CONCAT(:uid, '_')",
							)
			);
		$arrTrans[] = array(
			(!$this->hdnEmailID ?
					"INSERT INTO `_user_emails`(`CombinedID`, `UID`, `PendingEmail`, `IsPrivate`)"
					. " VALUES(:combid, :uid, :email, :isprv)" :
					"UPDATE `_user_emails` SET"
					. " `PendingEmail`=:email"
					. ", `IsPrivate`=:isprv"
					. " WHERE `CombinedID`=:combid"
			)
			, array(
				':combid' => $this->hdnEmailID? : $CombinedID,
				':uid' => $owner->drUser->ID,
				':email' => !$drEditingEmail || $drEditingEmail['Email'] != $this->txtEmail ?
						$this->txtEmail :
						null,
				':isprv' => $this->chkIsPrivate
			)
		);
		if (!$drEditingEmail || $drEditingEmail['Email'] != $this->txtEmail) {
			$this->_ActivationCode = T\DB::GetUniqueCode('_user_recoveries', 'Code');
			$this->_ActivationEmail = $this->txtEmail;
			$arrTrans[] = array(
				!$drEditingEmail['PendingEmail'] ?
						"INSERT INTO `_user_recoveries`(`UID`, `Code`, `TimeStamp`, `PendingEmail`, `EmailID`, `Type`)"
						. " VALUES(:uid, :code, :time, :email, :emailid, :emailverify)" :
						"UPDATE `_user_recoveries` SET `Code`=:code, `TimeStamp`=:time, `PendingEmail`=:email"
						. " WHERE `EmailID`=:emailid",
				array(
					':uid' => $owner->drUser->ID,
					':code' => $this->_ActivationCode,
					':time' => time(),
					':email' => $this->txtEmail,
					':emailverify' => C\User::Recovery_EmailVerify,
					':emailid' => $this->hdnEmailID? : $CombinedID,
				)
			);
		}
		if (!$this->hdnEmailID)
			$this->hdnEmailID = $CombinedID;
		$owner->addTransactions($arrTrans);
	}

	public function onSetForm(\CEvent $e) {
		$this->raiseEvent('onSetForm', $e);
		$owner = $this->owner;
		$drEmails = $this->getdtEmails($this->hdnEmailID);
		if ($drEmails) {
			$drEmails = $drEmails[0];
			$arrAttrs = array(
				'hdnEmailID' => $drEmails['CombinedID'],
				'txtEmail' => $drEmails['Email'],
				'chkIsPrivate' => $drEmails['IsPrivate'],
			);
			$owner->attributes = $arrAttrs;
			$this->_IsPrimaryEmailEdit = $drEmails['IsPrimary'];
			$this->_PendingEmail = $drEmails['PendingEmail'];
		}
	}

	public function ResetActivationLink() {
		$drActivationContact = T\DB::GetRow(
						"SELECT `PendingEmail`, `CombinedID`"
						. " FROM `_user_emails`"
						. " WHERE `CombinedID`=:combid"
						, array(
					':combid' => $this->hdnEmailID
						)
		);
		if ($drActivationContact) {
			$this->_ActivationCode = T\DB::GetUniqueCode('_user_recoveries', 'Code');
			$this->_ActivationEmail = $drActivationContact['PendingEmail'];
			T\DB::Execute(
					"UPDATE `_user_recoveries` SET `Code`=:code, `TimeStamp`=:time"
					. " WHERE `EmailID`=:emailid", array(
				':code' => $this->_ActivationCode,
				':time' => time(),
				':emailid' => $drActivationContact['CombinedID'],
					)
			);
		}
	}

	public function SetAsPrimary() {
		T\DB::Transaction(array(
			array(
				"UPDATE `_user_emails` SET `IsPrimary`=NULL WHERE `UID`=:uid AND NOT ISNULL(`IsPrimary`)"
				, array(':uid' => $this->owner->drUser['ID'])),
			array(
				"UPDATE `_user_emails` SET `IsPrimary`=1 WHERE `CombinedID`=:id"
				, array(':id' => $this->hdnEmailID)),
		));
	}

	public function events() {
		return array_merge(parent::events(), array(
			'onSave' => 'onSave',
			'onDelete' => 'onDelete',
			'onSetForm' => 'onSetForm',
		));
	}

}
