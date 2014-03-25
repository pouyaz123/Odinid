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
			'hdnRsdCountryID',
			'ddlResidencyStatus',
		));
	}

	public $hdnRsdCountryID;
	public $ddlCountry;
	public $txtCountry;
	public $ddlResidencyStatus;
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
			array('hdnRsdCountryID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnRsdCountryID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_residencies`'
				. ' WHERE `UID`=:uid AND (`GeoCountryISO2`=:val OR `UserCountryID`=:val)',
				'SQLParams' => array(':uid' => $owner->drUser->ID),
				'on' => 'Edit, Delete'),
			array('ddlCountry, txtCountry', 'required',
				'except' => 'Delete'),
			array('ddlCountry, txtCountry', 'match',
				'pattern' => C\Regexp::SimpleWords,
				'on' => 'CompanyRegister'),
			array_merge(array('ddlCountry, txtCountry', 'length',
				'except' => 'Delete'), $vl->Country),
			array('ddlResidencyStatus', 'in',
				'range' => array_keys($this->arrResidencyStatuses),
				'except' => 'Delete'),
//mytodo 2: delete these if residency works well (txtVisaType), also delete the method public ValidateVisaType
//			array_merge(array('txtVisaType', 'length',
//				'except' => 'Delete'), $vl->ResidencyVisa),
//			array('txtVisaType', 'ValidateVisaType', 'except' => 'Delete'),
		));
	}

//	public function ValidateVisaType($attr) {
//		if ($this->ddlResidencyStatus == self::RS_Visa && !$this->txtVisaType)
//			$this->owner->addError($attr
//					, \Yii::t('yii', '{attribute} cannot be blank.', array(
//						'{attribute}' => $this->owner->getAttributeLabel($attr)
//					))
//			);
//	}

	protected function beforeValidate($event) {
		if ($this->ddlResidencyStatus == self::RS_Visa) {
			#required txtVisaType
			$rv = new \CRequiredValidator();
			$rv->attributes = array('txtVisaType');
			$rv->except = array('Delete');
			$this->owner->validatorList->add($rv);
			#length txtVisaType
			$vl = \ValidationLimits\User::GetInstance();
			$lenv = new \CStringValidator();
			$lenv->attributes = array('txtVisaType');
			T\Basics::ConfigureObject($lenv, $vl->ResidencyVisa);
			$lenv->except = array('Delete');
			$this->owner->validatorList->add($lenv);
		}
	}

	public function afterValidate(\CEvent $event) {
		if ($this->owner->scenario != 'Delete')
			$this->ValidateNewResidency();
	}

	/**
	 * Validates the maximum numbre of residencies
	 */
	private function ValidateNewResidency() {
		if (!$this->hdnRsdCountryID) {//means in add mode not edit mode
			if (T\DB::GetField("SELECT COUNT(*) FROM `_user_residencies` WHERE `UID`=:uid") >= T\Settings::GetValue('MaxUserResidencies'))
				$this->owner->addError('', \t2::Site_Common('You have reached the maximum'));
			return;
		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'ddlCountry' => \t2::Site_Common('Country'),
			'txtCountry' => \t2::Site_Common('Country'),
			'ddlResidencyStatus' => \t2::Site_Common('Residency Status'),
			'txtVisaType' => \t2::Site_Common('Visa Type'),
		));
	}

	public function getdtResidencies() {
		static $dt = null;
		if (!$dt) {
			$dt = T\DB::GetTable(
							"SELECT "
							. " ur.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. " FROM `_user_residencies` ur"
							. " INNER JOIN (SELECT 1) tmp ur.`UID`=:uid"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=ur.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=ur.`UserCountryID`"
							, array(':uid' => $this->owner->drUser->ID)
			);
		}
		return $dt;
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
				"DELETE FROM `_user_residencies`"
				. " WHERE `UID`=:uid AND (`GeoCountryISO2`=:rsdcountry OR `UserCountryID`=:rsdcountry)",
				array(':rsdcountry' => $this->hdnRsdCountryID)
			)
		));
	}

	//remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		$arrTransactions = array();
		$arrTransactions[] = array(
			"CALL geo_getGeoLocationIDs(
						:country
						, NULL
						, NULL
						, @info_CountryISO2
						, @info_DivisionCombined
						, @info_DivisionCode
						, @info_CityID);
					CALL geo_getUserLocationIDs(
						:country
						, NULL
						, NULL
						, @info_CountryISO2
						, @info_DivisionCombined
						, @info_CityID
						, @info_UserCountryID
						, @info_UserDivisionID
						, @info_UserCityID)"
			, array(
				':country' => $this->txtCountry? : $this->ddlCountry,
			)
		);
		$arrTransactions[] = array(
			(!$this->hdnRsdCountryID ?
					"INSERT INTO `_user_residencies` SET"
					. " `UID`=:uid"
					. ", `GeoCountryISO2`=@info_CountryISO2"
					. ", `UserCountryID`=@info_UserCountryID"
					. ", `ResidencyStatus`=:rsdstatus"
					. ", `VisaType`=:visatype" :
					"UPDATE `_user_residencies` SET"
					. " `GeoCountryISO2`=@info_CountryISO2"
					. ", `UserCountryID`=@info_UserCountryID"
					. ", `ResidencyStatus`=:rsdstatus"
					. ", `VisaType`=:visatype"
					. " WHERE `UID`=:uid"
					. " AND (`GeoCountryISO2`=@info_CountryISO2 OR `UserCountryID`=@info_UserCountryID)"
			)
			, array(
				':uid' => $owner->drUser->ID,
				':rsdstatus' => $this->ddlResidencyStatus,
				':visatype' => $this->txtVisaType,
			)
		);
		$owner->addTransactions($arrTransactions);
	}

}
