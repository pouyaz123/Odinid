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
 * @property-write event $onSave push on save event handlers here
 * @property-read array $arrPhoneTypes user contacts
 * @property-read array $dtContacts user contacts
 * @property-read boolean $IsPrimaryEmailEdit
 * @property-read string $ActivationCode
 * @property Info $owner
 */
class Info_Contacts extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'hdnContactID, ddlPhoneType',
//			, txtEmailRepeat
		));
	}

	public $hdnContactID;
	public $txtPhone;
	public $ddlPhoneType = 'Mobile';
	public $txtEmail;
	//company contact
	public $txtContactFirstName;
	public $txtContactLastName;
	public $txtContactJobTitle;
	#
	private $_IsPrimaryEmailEdit = false;

	public function getIsPrimaryEmailEdit() {
		return $this->_IsPrimaryEmailEdit;
	}

//	private $_PendingEmail = null;
//
//	public function getPendingEmail() {
//		return $this->_PendingEmail;
//	}

	private $_ActivationCode = null;

	function getActivationCode() {
		return $this->_ActivationCode;
	}

	public function getarrPhoneTypes() {
		return array(
			'Home' => 'Home',
			'Work' => 'Work',
			'Mobile' => 'Mobile',
		);
	}

	public function onBeforeRules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		$e->params['arrRules'] = array_merge($e->params['arrRules'], array(
			array('hdnContactID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnContactID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_contactbook` WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->owner->drUser->ID),
				'on' => 'Edit, Delete'),
			array_merge(array('txtPhone', 'length',
				'except' => 'Delete'), $vl->Phone),
			array('txtPhone', 'match',
				'pattern' => C\Regexp::Phone,
				'except' => 'Delete'),
			array_merge(array('txtEmail', 'length',
				'except' => 'Delete'), $vl->Email),
			array('txtEmail', 'email',
				'except' => 'Delete'),
//			array('txtEmailRepeat', 'compare',
//				'compareAttribute' => 'txtEmail',
//				'except' => 'Delete'),
//			array('txtEmail', 'IsUnique',
//				'SQL' => 'SELECT COUNT(*) FROM `_user_contactbook` WHERE `Email`=:val AND `CombinedID`!=:combid',
//				'SQLParams' => array(':combid' => $this->hdnContactID),
//				'except' => 'Delete'),
			#
			array('ddlPhoneType', 'in',
				'range' => array_keys($this->arrPhoneTypes),
				'except' => 'Delete'),
		));
	}

	public function beforeValidate($event) {
		$owner = $this->owner;
		if ($this->txtEmail) {
			$unq = new \Validators\DBNotExist();
			$unq->attributes = array('txtEmail');
			$unq->SQL = 'SELECT COUNT(*) FROM `_user_contactbook` WHERE '
					. ($owner->scenario == 'Edit' || $this->hdnContactID ? ' `CombinedID`!=:combid AND ' : '')
					. ' `Email`=:val';
			$unq->SQLParams = array(':combid' => $this->hdnContactID);
			$unq->except = array('Delete');
			$owner->validatorList->add($unq);
		}
		if ($this->txtPhone) {
			#required ddlPhoneType
			$req = new \CRequiredValidator();
			$req->attributes = array('ddlPhoneType');
			$req->except = array('Delete');
			$owner->validatorList->add($req);
		} else {
			$this->ddlPhoneType = null;
		}
		#company contact info validators
		if ($owner->asa('Info_Company')) {
			$vl = \ValidationLimits\User::GetInstance();
			$validatorList = $owner->validatorList;
			$fncAddLenValidator = function($AttrName, $Limits)use($validatorList) {
				$lenv = new \CStringValidator();
				$lenv->attributes = array($AttrName);
				T\Basics::ConfigureObject($lenv, $Limits);
				$lenv->except = array('Delete');
				$validatorList->add($lenv);
			};
			$fncAddLenValidator('txtContactFirstName', $vl->FirstName);
			$fncAddLenValidator('txtContactLastName', $vl->LastName);
			$fncAddLenValidator('txtContactJobTitle', $vl->Title);
		}
	}

	public function afterValidate(\CEvent $event) {
		if ($this->owner->scenario != 'Delete')
			$this->ValidateNewContact();
	}

	/**
	 * Validates the maximum numbre of contacts and uniqueness of a contact field such as phone
	 */
	private function ValidateNewContact() {
		$dt = $this->dtContacts;
		$owner = $this->owner;
		if (!$this->hdnContactID) {//means in add mode not edit mode
			if (count($dt) >= T\Settings::GetValue('MaxUserContacts'))
				$owner->addError('', \t2::Site_User('You reached the maximum number of contacts'));
			return;
		}
//		$fncChkUnq = function($dr, $DBField, $ModelAttr)use($owner) {
//			if ($dr[$DBField] && $dr[$DBField] === $this->$ModelAttr)
//				$owner->addError($ModelAttr, \Yii::t('yii', '{attribute} "{value}" has already been taken.', array(
//							'{attribute}' => $owner->getAttributeLabel($ModelAttr),
//							'{value}' => $this->$ModelAttr,
//				)));
//		};
//		foreach ($dt as $dr) {
//			if ($this->hdnContactID && $dr['CombinedID'] === $this->hdnContactID)
//				continue;
//			$fncChkUnq($dr, 'Phone', 'txtPhone');
//			$fncChkUnq($dr, 'Email', 'txtEmail');
//		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'txtPhone' => \t2::Site_User('Phone'),
			'ddlPhoneType' => \t2::Site_User('Phone Type'),
			'txtEmail' => \t2::Site_User('Email'),
			#
			'txtContactFirstName' => \t2::Site_Company('Contact First Name'),
			'txtContactLastName' => \t2::Site_Company('Contact Last Name'),
			'txtContactJobTitle' => \t2::Site_Company('Contact Job Title'),
		));
	}

	public function getdtContacts($ContactID = NULL) {
		$StaticIndex = $ContactID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex])) {
			$arrDTs[$StaticIndex] = T\DB::GetTable("SELECT *"
							. " FROM `_user_contactbook` uc"
							. " INNER JOIN (SELECT 1) tmp ON uc.`UID`=:uid" . ($ContactID ? " AND uc.CombinedID=:contactid " : "")
							. ($this->owner->asa('Info_Company') ?
									" LEFT JOIN _company_contactinfo cci ON cci.ContactCombinedID = uc.CombinedID" :
									'')
							. " ORDER BY uc.`OrderNumber`"
							, array(
						':uid' => $this->owner->drUser->ID,
						':contactid' => $ContactID,
							)
			);
		}
		return $arrDTs[$StaticIndex]? : array();
	}

	public function events() {
		return array_merge(parent::events(), array(
			'onSave' => 'onSave',
			'onDelete' => 'onDelete',
			'onSetForm' => 'onSetForm',
		));
	}

	public function onDelete(\CEvent $e) {
		$this->raiseEvent('onDelete', $e);
		$this->owner->addTransactions(array(
			array(
				"DELETE FROM `_user_contactbook` WHERE `CombinedID`=:combid AND ISNULL(`IsPrimary`)",
				array(':combid' => $this->hdnContactID)
			)
		));
	}

	//remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		if ((!$this->txtPhone && !$this->txtEmail) || $owner->scenario == 'Delete') {
			if ($owner->scenario == 'Edit')
				$owner->Delete();
			return false;
		}
		$CombinedID = !$this->hdnContactID ? T\DB::GetNewID_Combined(
						'_user_contactbook'
						, 'CombinedID'
						, 'UID=:uid'
						, array(':uid' => $owner->drUser->ID)
						, array(
					'PrefixQuery' => "CONCAT(:uid, '_')",
					'ReturnTheQuery' => false
						)
				) : $this->hdnContactID;
		$arrTrans = array();
		$GenerateNewActivationCode = ($owner->scenario == 'Add');
		$drEmails = null;
		if ($owner->scenario != 'Add') {
			$drEmails = T\DB::GetRow("SELECT `Email`, `PendingEmail` FROM `_user_contactbook` WHERE `CombinedID`=:combid"
							, array(
						':combid' => $this->hdnContactID
							)
			);
			if ($drEmails)
				$GenerateNewActivationCode = ($drEmails['PendingEmail'] != $this->txtEmail && $drEmails['Email'] != $this->txtEmail);
			if ((!$this->txtEmail || $drEmails['Email'] == $this->txtEmail) && $drEmails['PendingEmail']) {
				$arrTrans[] = array(
					"DELETE FROM `_user_recoveries` WHERE `UID`=:uid AND `PendingEmail`=:email",
					array(
						':uid' => $owner->drUser->ID,
						':pendingemail' => $drEmails['PendingEmail'],
					)
				);
			}
		}
		$arrTrans[] = array(
			(!$this->hdnContactID ?
					"INSERT INTO `_user_contactbook` SET"
					. " `CombinedID`=:combid"
					. ", `UID`=:uid"
					. ", `Phone`=:phone"
					. ", `PhoneType`=:phonetype"
					. ", `PendingEmail`=:pendingemail" :
					"UPDATE `_user_contactbook` SET"
					. " `Phone`=:phone"
					. ", `PhoneType`=:phonetype"
					. ", `PendingEmail`=:pendingemail"
					. " WHERE `CombinedID`=:combid"
			)
			, array(
				':combid' => $this->hdnContactID? : $CombinedID,
				':uid' => $owner->drUser->ID,
				':phone' => $this->txtPhone? : null,
				':phonetype' => $this->txtPhone && $this->ddlPhoneType ? $this->ddlPhoneType : null,
				':pendingemail' => $GenerateNewActivationCode ? $this->txtEmail : null,
			)
		);
		if ($GenerateNewActivationCode) {
			$this->_ActivationCode = T\DB::GetUniqueCode('_user_recoveries', 'Code');
			$arrTrans[] = array("INSERT INTO `_user_recoveries`(`UID`, `Code`, `TimeStamp`, `PendingEmail`, `Type`)"
				. " VALUES(:uid, :code, :time, :email, :emailverify)",
				array(
					':uid' => $owner->drUser->ID,
					':code' => $this->_ActivationCode,
					':time' => time(),
					':email' => $this->txtEmail,
					':emailverify' => C\User::Recovery_EmailVerify,
				)
			);
		}
		$this->hdnContactID = $CombinedID;
		if ($owner->asa('Info_Company')) {
			if (!$this->txtContactFirstName && $this->txtContactLastName && $this->txtContactJobTitle)
				$arrTrans[] = array(
					"DELETE FROM `_company_contactinfo` WHERE `ContactCombinedID`=:cntctid AND `UID`=:uid"
					, array(
						':uid' => $owner->drUser->ID,
						':cntctid' => $this->hdnContactID,
					)
				);
			else
				$arrTrans[] = array(
					"INSERT INTO `_company_contactinfo` SET "
					. " `UID`=:uid"
					. ", `ContactCombinedID`=:cntctid"
					. ", `FirstName`=:cfname"
					. ", `LastName`=:clname"
					. ", `JobTitle`=:cjobtitle"
					. " ON DUPLICATE KEY UPDATE "
					. " `FirstName`=:cfname"
					. ", `LastName`=:clname"
					. ", `JobTitle`=:cjobtitle"
					, array(
						':uid' => $owner->drUser->ID,
						':cntctid' => $this->hdnContactID,
						':cfname' => $this->txtContactFirstName? : null,
						':clname' => $this->txtContactLastName? : null,
						':cjobtitle' => $this->txtContactJobTitle? : null,
					)
				);
		}
		$owner->addTransactions($arrTrans);
	}

	public function onSetForm(\CEvent $e) {
		$this->raiseEvent('onSetForm', $e);
		$owner = $this->owner;
		$drContact = $this->getdtContacts($this->hdnContactID);
		if ($drContact) {
			$drContact = $drContact[0];
			$arrAttrs = array(
				'hdnContactID' => $drContact['CombinedID'],
				'txtPhone' => $drContact['Phone'],
				'ddlPhoneType' => $drContact['PhoneType'],
				'txtEmail' => $drContact['PendingEmail']? : $drContact['Email'],
			);
			if ($owner->asa('Info_Company')) {
				$arrAttrs['txtContactFirstName'] = $drContact['FirstName'];
				$arrAttrs['txtContactLastName'] = $drContact['LastName'];
				$arrAttrs['txtContactJobTitle'] = $drContact['JobTitle'];
			}
			$owner->attributes = $arrAttrs;
			$this->_IsPrimaryEmailEdit = $drContact['IsPrimary'];
//			$this->_PendingEmail = $drContact['PendingEmail'];
		}
	}

}
