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
 * @property-read array $dtResidencies
 * @property-read array $arrResidencyStatuses
 * @property Info $owner
 */
class Info_Residencies extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'hdnResidencyID',
			'rdoResidencyStatus',
		));
	}

	public $hdnResidencyID;
	public $ddlCountry;
	public $txtCountry;
	public $rdoResidencyStatus;
	public $txtVisaType;

	//Residency Status (RS)
	const RS_Citizen = 'Citizen';
	const RS_Permanent = 'Permanent';
	const RS_Visa = 'Visa';

	function getarrResidencyStatuses() {
		return array(
			self::RS_Citizen => self::RS_Citizen,
			self::RS_Permanent => self::RS_Permanent,
			self::RS_Visa => self::RS_Visa,
		);
	}

	public function onBeforeRules(\CEvent $e) {
		$owner = $this->owner;
		$vl = \ValidationLimits\User::GetInstance();
		$e->params['arrRules'] = array_merge($e->params['arrRules'], array(
			array('hdnResidencyID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnResidencyID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_residencies`'
				. ' WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $owner->drUser->ID),
				'on' => 'Edit, Delete'),
			array('ddlCountry, txtCountry, rdoResidencyStatus', 'required',
				'on' => 'Add, Edit'),
			array('ddlCountry, txtCountry', 'match',
				'pattern' => C\Regexp::SimpleWords,
				'on' => 'Add, Edit'),
			array_merge(array('ddlCountry, txtCountry', 'length',
				'on' => 'Add, Edit'), $vl->Country),
			array('rdoResidencyStatus', 'in',
				'range' => array_keys($this->arrResidencyStatuses),
				'on' => 'Add, Edit'),
			array_merge(array('txtVisaType', 'length',
				'on' => 'Add, Edit'), $vl->ResidencyVisa),
		));
	}

	public function beforeValidate($event) {
		if ($this->rdoResidencyStatus == self::RS_Visa) {
			#required txtVisaType
			$rv = new \CRequiredValidator();
			$rv->attributes = array('txtVisaType');
			$rv->except = 'Delete';
			$this->owner->validatorList->add($rv);
		} else
			$this->txtVisaType = NULL;
		$owner = $this->owner;
		$unq = new \Validators\DBNotExist();
		$unq->attributes = array('ddlCountry', 'txtCountry');
		$unq->SQL = 'SELECT COUNT(*) FROM `_user_residencies` '
				. ' '
				. ' WHERE '
				. ($owner->scenario == 'Edit' || $this->hdnResidencyID ? ' `CombinedID`!=:id AND ' : '')
				. '  AND `UID`=:uid';
		$unq->SQLParams = array(
			':id' => $this->hdnResidencyID,
			':uid' => $owner->drUser['ID']
		);
		$unq->except = 'Delete';
		$owner->validatorList->add($unq);
//			array('ddlCountry, txtCountry', 'IsUnique',
//				'SQL' => 'SELECT COUNT(*) FROM `_user_residencies` AS ur'
//				. ' INNER JOIN (SELECT 1) AS tmp ON ur.`UID`=:uid'
//				. ' LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=ur.`UserCountryID`'
//				. ' WHERE (ur.`GeoCountryISO2`=:val OR guc.`Country`=:val)',
//				'SQLParams' => array(':uid' => $owner->drUser->ID),
//				'on' => 'Add, Edit'),
	}

	public function afterValidate(\CEvent $event) {
		if ($this->owner->scenario != 'Delete')
			$this->ValidateNewResidency();
	}

	/**
	 * Validates the maximum numbre of residencies
	 */
	private function ValidateNewResidency() {
		$dt = $this->dtResidencies;
		if (!$this->hdnResidencyID) {//means in add mode not edit mode
			if (count($dt) >= T\Settings::GetValue('MaxUserResidencies'))
				$this->owner->addError('', \t2::Site_Common('You have reached the maximum'));
			return;
		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'ddlCountry' => \t2::Site_User('Country'),
			'txtCountry' => \t2::Site_User('Country'),
			'rdoResidencyStatus' => \t2::Site_User('Residency Status'),
			'txtVisaType' => \t2::Site_User('Visa Type'),
		));
	}

	public function getdtResidencies($ID = NULL, $refresh = false) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = null;
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT "
							. " ur.*"
							. " , IFNULL(ur.`GeoCountryISO2`, ur.`UserCountryID`) AS ID"
							. " , IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. " FROM `_user_residencies` ur"
							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " CombinedID=:id AND " : "") . " ur.`UID`=:uid "
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=ur.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=ur.`UserCountryID`"
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
	public function getdtFreshResidencies($ID = null) {
		static $Result = null;
		if (!$Result)
			$Result = $this->getdtResidencies($ID, true);
		return $Result;
	}

	public function onDelete(\CEvent $e) {
		$this->raiseEvent('onDelete', $e);
		$this->owner->addTransactions(array(
			array(
				"DELETE FROM `_user_residencies`"
				. " WHERE `CombinedID`=:id AND `UID`=:uid",
				array(
					':uid' => $this->owner->drUser['ID'],
					':id' => $this->hdnResidencyID,
				)
			)
		));
	}

	//remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		$CombinedID = $this->hdnResidencyID? : T\DB::GetNewID_Combined(
						'_user_residencies'
						, 'CombinedID'
						, 'UID=:uid'
						, array(':uid' => $owner->drUser->ID)
						, array(
					'PrefixQuery' => "CONCAT(:uid, '_')",
					'ReturnTheQuery' => false
						)
		);
		$arrTransactions = array();
		$arrTransactions[] = array(
			"CALL geo_getGeoLocationIDs(
						:country
						, NULL
						, NULL
						, @rsdnc_CountryISO2
						, @rsdnc_DivisionCombined
						, @rsdnc_DivisionCode
						, @rsdnc_CityID);
					CALL geo_getUserLocationIDs(
						:country
						, NULL
						, NULL
						, @rsdnc_CountryISO2
						, @rsdnc_DivisionCombined
						, @rsdnc_CityID
						, @rsdnc_UserCountryID
						, @rsdnc_UserDivisionID
						, @rsdnc_UserCityID)"
			, array(
				':country' => $this->txtCountry? : $this->ddlCountry,
			)
		);
		$arrTransactions[] = array(
			(!$this->hdnResidencyID ?
					"INSERT INTO `_user_residencies` SET"
					. " CombinedID=:id"
					. ", `UID`=:uid"
					. ", `GeoCountryISO2`=@rsdnc_CountryISO2"
					. ", `UserCountryID`=@rsdnc_UserCountryID"
					. ", `ResidencyStatus`=:rsdstatus"
					. ", `VisaType`=:visatype" :
					"UPDATE `_user_residencies` SET"
					. " `GeoCountryISO2`=@rsdnc_CountryISO2"
					. ", `UserCountryID`=@rsdnc_UserCountryID"
					. ", `ResidencyStatus`=:rsdstatus"
					. ", `VisaType`=:visatype"
					. " WHERE CombinedID=:id AND `UID`=:uid"
			)
			, array(
				':uid' => $owner->drUser->ID,
				':rsdstatus' => $this->rdoResidencyStatus,
				':visatype' => $this->txtVisaType,
				':id' => $CombinedID,
			)
		);
		$this->hdnResidencyID = $CombinedID;
		$owner->addTransactions($arrTransactions);
	}

	public function onSetForm(\CEvent $e) {
		$this->raiseEvent('onSetForm', $e);
		$owner = $this->owner;
		$dr = $this->getdtResidencies($this->hdnResidencyID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnResidencyID' => $dr['CombinedID'],
				'ddlCountry' => $dr['GeoCountryISO2']? : '_other_',
				'txtCountry' => $dr['GeoCountryISO2'] ? : $dr['Country'],
				'rdoResidencyStatus' => $dr['ResidencyStatus'],
				'txtVisaType' => $dr['VisaType'],
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
