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
 * @property-read array $arrWebAddrTypes
 * @property-read array $dtWebAddr user web addresses
 * @property Info $owner
 */
class Info_WebAddresses extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'hdnWebAddrID',
			'ddlWebAddrType',
		));
	}

	public $hdnWebAddrID;
	public $txtWebAddress;
	public $ddlWebAddrType;
	public $txtCustomType;

	public function getarrWebAddrTypes() {
		return array(
			'Linkedin' => 'Linkedin',
			'Twitter' => 'Twitter',
			'Facebook' => 'Facebook',
			'Fliker' => 'Fliker',
			'Instagram' => 'Instagram',
			'Behance' => 'Behance',
		);
	}

	public function onBeforeRules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		$e->params['arrRules'] = array_merge($e->params['arrRules'], array(
			array('hdnWebAddrID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnWebAddrID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_webaddresses` WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->owner->drUser->ID),
				'on' => 'Edit, Delete'),
			array_merge(array('txtWebAddress', 'length',
				'except' => 'Delete'), $vl->WebAddress),
			#
			array('ddlWebAddrType', 'in',
				'range' => array_keys($this->arrWebAddrTypes),
				'except' => 'Delete'),
			array_merge(array('txtCustomType', 'length',
				'except' => 'Delete'), $vl->CustomType),
		));
	}

	protected function beforeValidate($event) {
		if (!$this->ddlWebAddrType) {
			#required txtCustomType
			$rv = new \CRequiredValidator();
			$rv->attributes = array('txtCustomType');
			$rv->except = array('Delete');
			$this->owner->validatorList->add($rv);
			#length txtCustomType
			$vl = \ValidationLimits\User::GetInstance();
			$lenv = new \CStringValidator();
			$lenv->attributes = array('txtCustomType');
			T\Basics::ConfigureObject($lenv, $vl->CustomType);
			$lenv->except = array('Delete');
			$this->owner->validatorList->add($lenv);
		}
	}

	public function afterValidate(\CEvent $event) {
		if ($this->owner->scenario != 'Delete')
			$this->ValidateNewWebAddr();
	}

	/**
	 * Validates the maximum numbre of web addresses based on the settings (MaxUserContacts)
	 */
	private function ValidateNewWebAddr() {
		$dt = $this->dtWebAddr;
		$owner = $this->owner;
		if (!$this->hdnWebAddrID) {//means in add mode not edit mode
			if (count($dt) >= T\Settings::GetValue('MaxUserContacts'))
				$owner->addError('', \t2::Site_User('You reached the maximum number of web addresses'));
			return;
		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'txtWebAddress' => \t2::Site_User('Web address'),
			'ddlWebAddrType' => \t2::Site_User('Web address type'),
			'txtCustomType' => \t2::Site_User('Web address type'),
		));
	}

	public function getdtWebAddr() {
		static $dt = null;
		if (!$dt)
			$dt = T\DB::GetTable(
							"SELECT *"
							. " FROM `_user_webaddresses`"
							. " WHERE `UID`=:uid"
							. " ORDER BY `OrderNumber`"
							, array(':uid' => $this->owner->drUser->ID));
		return $dt? : array();
	}

	public function events() {
		return array_merge(parent::events(), array(
			'onSave' => 'onSave',
			'onDelete' => 'onDelete',
		));
	}

	public function onDelete(\CEvent $e) {
		$this->raiseEvent('onDelete', $e);
		$this->owner->addTransactions(array(
			array(
				"DELETE FROM `_user_webaddresses` WHERE `CombinedID`=:combid",
				array(':combid' => $this->hdnWebAddrID)
			)
		));
	}

	//remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		if (!$this->txtPhone && !$this->txtFax && !$this->txtMobile && !$this->txtEmail && !$this->txtWebAddress) {
			$owner->Delete();
			return false;
		}
		if (!$this->hdnWebAddrID)
			$strSQLPart_CombinedID = T\DB::GetNewID_Combined(
							'_user_webaddresses'
							, 'CombinedID'
							, 'UID=:uid'
							, NULL
							, array(
						'PrefixQuery' => "CONCAT(:uid, '_')",
							)
			);
		$arrTransactions = array();
		$arrTransactions[] = array(
			(!$this->hdnWebAddrID ?
					"INSERT INTO `_user_webaddresses` SET "
					. " `CombinedID`=($strSQLPart_CombinedID)"
					. ", `UID`=:uid"
					. ", `WebAddress`=:webaddr"
					. ", `Type`=:webaddrt"
					. ", `CustomTitle`=:customtype" :
					"UPDATE `_user_webaddresses` SET "
					. " `WebAddress`=:webaddr"
					. ", `Type`=:webaddrt"
					. ", `CustomTitle`=:customtype"
					. " WHERE `CombinedID`=:combid"
			)
			, array(
				':combid' => $this->hdnWebAddrID? : null,
				':uid' => $owner->drUser->ID,
				':webaddr' => $this->txtWebAddress? : null,
				':webaddrt' => $this->ddlWebAddrType? : null,
				':customtype' => $this->txtCustomType? : null,
			)
		);
		$owner->addTransactions($arrTransactions);
	}

}
