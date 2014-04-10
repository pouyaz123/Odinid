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
 * @property-read array $arrPhoneTypes
 * @property-read array $dtContacts
 * @property Info $owner
 */
class Info_Contacts extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'hdnContactID, ddlPhoneType',
		));
	}

	public $hdnContactID;
	public $txtPhone;
	public $ddlPhoneType = 'Mobile';
	//company contact
	public $txtContactFirstName;
	public $txtContactLastName;
	public $txtContactJobTitle;

	#

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
				'SQL' => 'SELECT COUNT(*) FROM `_user_contacts` WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->owner->drUser->ID),
				'on' => 'Edit, Delete'),
			array('txtPhone, ddlPhoneType', 'required',
				'on' => 'Add, Edit'),
			array_merge(array('txtPhone', 'length',
				'on' => 'Add, Edit'), $vl->Phone),
			array('txtPhone', 'match',
				'pattern' => C\Regexp::Phone,
				'on' => 'Add, Edit'),
			array('ddlPhoneType', 'in',
				'range' => array_keys($this->arrPhoneTypes),
				'on' => 'Add, Edit'),
		));
	}

	public function beforeValidate($event) {
		$owner = $this->owner;
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
				$owner->addError('', \t2::Site_Common('You have reached the maximum'));
			return;
		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'txtPhone' => \t2::Site_User('Phone'),
			'ddlPhoneType' => \t2::Site_User('Phone Type'),
			#
			'txtContactFirstName' => \t2::Site_Company('Contact First Name'),
			'txtContactLastName' => \t2::Site_Company('Contact Last Name'),
			'txtContactJobTitle' => \t2::Site_Company('Contact Job Title'),
		));
	}

	public function getdtContacts($ID = NULL, $refresh = false) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			$arrDTs[$StaticIndex] = T\DB::GetTable("SELECT *"
							. " FROM `_user_contacts` uc"
							. " INNER JOIN (SELECT 1) tmp ON uc.`UID`=:uid" . ($ID ? " AND uc.CombinedID=:id " : "")
							. ($this->owner->asa('Info_Company') ?
									" LEFT JOIN _company_contactinfo cci ON cci.ContactID = uc.CombinedID" :
									"")
							. " ORDER BY uc.`OrderNumber`"
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
	public function getdtFreshContacts($ID = null) {
		static $Result = null;
		if (!$Result)
			$Result = $this->getdtContacts($ID, true);
		return $Result;
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
				"DELETE FROM `_user_contacts` WHERE `CombinedID`=:id",
				array(':id' => $this->hdnContactID)
			)
		));
	}

	//remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		if (!$this->txtPhone || $owner->scenario == 'Delete') {
			if ($owner->scenario == 'Edit')
				$owner->Delete();
			return false;
		}
		$CombinedID = $this->hdnContactID ? : T\DB::GetNewID_Combined(
						'_user_contacts'
						, 'CombinedID'
						, 'UID=:uid'
						, array(':uid' => $owner->drUser->ID)
						, array(
					'PrefixQuery' => "CONCAT(:uid, '_')",
					'ReturnTheQuery' => false
						)
		);
		$arrTrans = array();
		$arrTrans[] = array(
			(!$this->hdnContactID ?
					"INSERT INTO `_user_contacts` SET"
					. " `CombinedID`=:id"
					. ", `UID`=:uid"
					. ", `Phone`=:phone"
					. ", `PhoneType`=:phonetype" :
					"UPDATE `_user_contacts` SET"
					. " `Phone`=:phone"
					. ", `PhoneType`=:phonetype"
					. " WHERE `CombinedID`=:id"
			)
			, array(
				':id' => $this->hdnContactID? : $CombinedID,
				':uid' => $owner->drUser->ID,
				':phone' => $this->txtPhone? : null,
				':phonetype' => $this->txtPhone && $this->ddlPhoneType ? $this->ddlPhoneType : null,
			)
		);
		if ($owner->asa('Info_Company')) {
			if (!$this->txtContactFirstName && !$this->txtContactLastName && !$this->txtContactJobTitle)
				$arrTrans[] = array(
					"DELETE FROM `_company_contactinfo` WHERE `ContactID`=:id"
					, array(
						':uid' => $owner->drUser->ID,
						':id' => $CombinedID,
					)
				);
			else
				$arrTrans[] = array(
					"INSERT INTO `_company_contactinfo`(`ContactID`, `FirstName`, `LastName`, `JobTitle`)"
					. " VALUES(:id, :cfname, :clname, :cjobtitle)"
					. " ON DUPLICATE KEY UPDATE "
					. " `FirstName`=:cfname"
					. ", `LastName`=:clname"
					. ", `JobTitle`=:cjobtitle"
					, array(
						':id' => $CombinedID,
						':cfname' => $this->txtContactFirstName? : null,
						':clname' => $this->txtContactLastName? : null,
						':cjobtitle' => $this->txtContactJobTitle? : null,
					)
				);
		}
		if (!$this->hdnContactID)
			$this->hdnContactID = $CombinedID;
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
			);
			if ($owner->asa('Info_Company')) {
				$arrAttrs = array_merge($arrAttrs, array(
					'txtContactFirstName' => $drContact['FirstName'],
					'txtContactLastName' => $drContact['LastName'],
					'txtContactJobTitle' => $drContact['JobTitle'],
				));
			}
			$owner->attributes = $arrAttrs;
		}
	}

}
