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
			#
			array('txtWebAddress, ddlWebAddrType, txtCustomType', 'required',
				'on' => 'Add, Edit'),
			array_merge(array('txtWebAddress', 'length',
				'on' => 'Add, Edit'), $vl->WebAddress),
			array('txtWebAddress', 'url',
				'on' => 'Add, Edit'),
//			array('ddlWebAddrType', 'in',
//				'range' => array_keys($this->arrWebAddrTypes),
//				'on' => 'Add, Edit'),
			array_merge(array('txtCustomType', 'length',
				'on' => 'Add, Edit'), $vl->CustomType),
		));
	}

	public function beforeValidate($event) {
		$owner = $this->owner;
		$unq = new \Validators\DBNotExist();
		$unq->attributes = array('txtWebAddress');
		$unq->SQL = 'SELECT COUNT(*) FROM `_user_webaddresses` WHERE '
				. ($owner->scenario == 'Edit' || $this->hdnWebAddrID ? ' `CombinedID`!=:combid AND ' : '')
				. ' `WebAddress`=:val AND `UID`=:uid';
		$unq->SQLParams = array(
			':combid' => $this->hdnWebAddrID,
			':uid' => $owner->drUser['ID']
		);
		$unq->except = 'Delete';
		$owner->validatorList->add($unq);
	}

	public function afterValidate(\CEvent $event) {
		if ($this->owner->scenario != 'Delete')
			$this->ValidateNewWebAddr();
	}

	/**
	 * Validates the maximum numbre of web addresses based on the settings (MaxUserContacts)
	 */
	private function ValidateNewWebAddr() {
		$owner = $this->owner;
		if (!$this->hdnWebAddrID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_user_webaddresses` WHERE `UID`=:uid"
							, array(':uid' => $owner->drUser['ID']));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxUserContacts)
				$owner->addError('', \t2::site_site('You have reached the maximum'));
		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'txtWebAddress' => \t2::site_site('Web address'),
			'ddlWebAddrType' => \t2::site_site('Web address type'),
			'txtCustomType' => \t2::site_site('Web address type'),
		));
	}

	public function getdtWebAddr($ID = NULL, $refresh = false) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = null;
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT *"
							. " FROM `_user_webaddresses`"
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
	public function getdtFreshWebAddr($ID = null) {
		static $F = true;
		$R = $this->getdtWebAddr($ID, $F);
		$F = false;
		return $R;
	}

	public function onDelete(\CEvent $e) {
		$this->raiseEvent('onDelete', $e);
		$this->owner->addTransactions(array(
			array(
				"DELETE FROM `_user_webaddresses` WHERE `CombinedID`=:combid AND `UID`=:uid",
				array(
					':uid' => $this->owner->drUser->ID,
					':combid' => $this->hdnWebAddrID,
				)
			)
		));
	}

	//remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
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
		$arrTrans = array();
		$arrTrans[] = array(
			(!$this->hdnWebAddrID ?
					"INSERT INTO `_user_webaddresses` SET "
					. " `CombinedID`=($strSQLPart_CombinedID)"
					. ", `UID`=:uid"
					. ", `WebAddress`=:webaddr"
					. ", `Type`=:webaddrt"
					. ", `CustomType`=:customtype" :
					"UPDATE `_user_webaddresses` SET "
					. " `WebAddress`=:webaddr"
					. ", `Type`=:webaddrt"
					. ", `CustomType`=:customtype"
					. " WHERE `CombinedID`=:combid AND `UID`=:uid"
			)
			, array(
				':combid' => $this->hdnWebAddrID? : null,
				':uid' => $owner->drUser->ID,
				':webaddr' => $this->txtWebAddress? : null,
				':webaddrt' => array_key_exists($this->ddlWebAddrType, $this->arrWebAddrTypes) ? $this->ddlWebAddrType : 'Other',
				':customtype' => $this->txtCustomType? : null,
			)
		);
		$owner->addTransactions($arrTrans);
	}

	public function onSetForm(\CEvent $e) {
		$this->raiseEvent('onSetForm', $e);
		$owner = $this->owner;
		$dr = $this->getdtWebAddr($this->hdnWebAddrID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnWebAddrID' => $dr['CombinedID'],
				'txtWebAddress' => $dr['WebAddress'],
				'ddlWebAddrType' => $dr['Type'] != 'Other' ? $dr['Type'] : '_other_',
				'txtCustomType' => $dr['CustomType'],
			);
			$owner->attributes = $arrAttrs;
		}
	}

	public function events() {
		return array_merge(parent::events(), array(
			'onSave' => 'onSave',
			'onDelete' => 'onDelete',
			'onSetForm' => 'onSetForm',
		));
	}

}
