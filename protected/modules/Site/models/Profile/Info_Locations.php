<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * attach Info_Locations after Info_Contacts if you want to use them together
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-write event $onSave push on save event handlers here
 * @property-read array $dtLocations user locations
 * @property-read array $drCurrentLocation
 * @property-read array $drBillingLocation
 * @property Info $owner
 */
class Info_Locations extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'hdnLocationID',
			'chkIsCurrentLocation',
			'chkIsBillingLocation',
		));
	}

	public $hdnLocationID;
	//location
	public $ddlCountry;
	public $ddlDivision;
	public $ddlCity;
	public $txtCountry;
	public $txtDivision;
	public $txtCity;
	public $txtAddress1;
	public $txtAddress2;
	public $txtPostalCode;
	public $chkIsCurrentLocation;
	public $chkIsBillingLocation;

	public function onBeforeRules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		$e->params['arrRules'] = array_merge($e->params['arrRules'], array(
			array('hdnLocationID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnLocationID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_locations` WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->owner->drUser->ID),
				'on' => 'Edit, Delete'),
			array('ddlCountry, txtCountry, ddlDivision, txtDivision, ddlCity, txtCity', 'required'
				, 'except' => 'Delete'),
			array('ddlCountry, ddlDivision, ddlCity, txtCountry, txtDivision, txtCity', 'match',
				'pattern' => C\Regexp::SimpleWords,
				'except' => 'Delete'),
			array_merge(array('ddlCountry, txtCountry', 'length',
				'except' => 'Delete'), $vl->Country),
			array_merge(array('ddlDivision, txtDivision', 'length',
				'except' => 'Delete'), $vl->Division),
			array_merge(array('ddlCity, txtCity', 'length',
				'except' => 'Delete'), $vl->City),
			array_merge(array('txtAddress1, txtAddress2', 'length',
				'except' => 'Delete'), $vl->Address),
			array_merge(array('txtPostalCode', 'length',
				'except' => 'Delete'), $vl->PostalCode),
			array('chkIsCurrentLocation, chkIsBillingLocation', 'boolean',
				'except' => 'Delete'),
		));
	}

	public function afterValidate(\CEvent $event) {
		if ($this->owner->scenario != 'Delete')
			$this->ValidateNewLocation();
	}

	/**
	 * Validates the maximum numbre of Locations and uniqueness of a Location
	 */
	private function ValidateNewLocation() {
		$dt = $this->dtLocations;
		if (!$this->hdnLocationID) {//means in add mode not edit mode
			if (count($dt) >= T\Settings::GetValue('MaxUserLocations'))
				$this->owner->addError('', \t2::Site_User('You reached the maximum number of locations'));
			return false;
		}
		foreach ($dt as $dr) {
			if ($this->hdnLocationID && $dr['CombinedID'] === $this->hdnLocationID)
				continue;
			if (
					($dr['GeoCountryISO2'] == $this->ddlCountry || $dr['Country'] == $this->txtCountry) &&
					($dr['GeoDivisionCode'] == $this->ddlDivision || $dr['Division'] == $this->txtDivision) &&
					($dr['GeoCityID'] == $this->ddlCity || $dr['City'] == $this->txtCity) &&
					$dr['Address1'] == $this->txtAddress1 && $dr['Address2'] == $this->txtAddress2
			)
				$this->owner->addError('', \t2::Site_User('This geographical location has been used previously'));
		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'ddlCountry' => \t2::Site_Common('Country'),
			'ddlDivision' => \t2::Site_Common('Division'),
			'ddlCity' => \t2::Site_Common('City'),
			'txtCountry' => \t2::Site_Common('Country'),
			'txtDivision' => \t2::Site_Common('Division'),
			'txtCity' => \t2::Site_Common('City'),
			'txtAddress1' => \t2::Site_Common('Address 1'),
			'txtAddress2' => \t2::Site_Common('Address 2'),
			'txtPostalCode' => \t2::Site_Common('Postal code'),
			'chkIsCurrentLocation' => \t2::Site_Common('Is current location'),
			'chkIsBillingLocation' => \t2::Site_Common('Is Billing location'),
		));
	}

	public function getdrCurrentLocation() {
		static $dr = null;
		if (!$dr) {
			$dr = T\DB::GetRow(
							"SELECT "
							. " locs.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", gd.`DivisionCode`"
							. ", IFNULL(gct.`AsciiName`, guct.`City`) AS City"
							. " FROM `_user_locations` AS locs"
							. " INNER JOIN (SELECT 1) AS tmp ON locs.`UID`=:uid AND locs.`IsCurrentLocation`"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=locs.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=locs.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =locs.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=locs.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=locs.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=locs.`UserCityID`"
							, array(':uid' => $this->owner->drUser->ID));
		}
		return $dr;
	}

	public function getdrBillingLocation() {
		static $dr = null;
		if (!$dr) {
			$dr = T\DB::GetRow(
							"SELECT "
							. " locs.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", IFNULL(gct.`AsciiName`, guct.`City`) AS City"
							. " FROM `_user_locations` AS locs"
							. " INNER JOIN (SELECT 1) AS tmp ON locs.`UID`=:uid AND locs.`IsBillingLocation`"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=locs.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=locs.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =locs.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=locs.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=locs.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=locs.`UserCityID`"
							, array(':uid' => $this->owner->drUser->ID));
		}
		return $dr;
	}

	public function getdtLocations() {
		static $dt = null;
		if (!$dt) {
			//mytodo x: in Info_Locations i think we can query faster if we put these joins in a procedure
			$dt = T\DB::GetTable(
							"SELECT "
							. " locs.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", IFNULL(gct.`AsciiName`, gud.`City`) AS City"
							. " FROM `_user_locations` AS locs"
							. " INNER JOIN (SELECT 1) AS tmp ON locs.`UID`=:uid"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=locs.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=locs.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =locs.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=locs.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=locs.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=locs.`UserCityID`"
							, array(':uid' => $this->owner->drUser->ID));
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
				"DELETE FROM `_user_locations` WHERE `CombinedID`=:combid",
				array(':combid' => $this->hdnLocationID)
			)
		));
	}

	//remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		if ($this->hdnLocationID)
			$CombinedID = $this->hdnLocationID;
		else
			$CombinedID = T\DB::GetNewID_Combined(
							'_user_locations'
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
					, :division
					, :city
					, @info_CountryISO2
					, @info_DivisionCombined
					, @info_DivisionCode
					, @info_CityID);
				CALL geo_getUserLocationIDs(
					:country
					, :division
					, :city
					, @info_CountryISO2
					, @info_DivisionCombined
					, @info_CityID
					, @info_UserCountryID
					, @info_UserDivisionID
					, @info_UserCityID)"
			, array(
				':country' => $this->txtCountry? : $this->ddlCountry,
				':division' => $this->txtDivision? : $this->ddlDivision,
				':city' => $this->txtCity? : $this->ddlCity,
			)
		);
		//i don't remember why had i written this part of code
		/* //		if ($this->chkIsCurrentLocation || $this->chkIsBillingLocation)
		  //			$arrTransactions[] = array(
		  //				"UPDATE `_user_locations` SET"
		  //				. ($this->chkIsCurrentLocation ? " `IsCurrentLocation`=null" : "")
		  //				. ($this->chkIsCurrentLocation && $this->chkIsBillingLocation ? "," : "")
		  //				. ($this->chkIsBillingLocation ? " `IsBillingLocation`=null" : "")
		  //				. " WHERE `UID`=:uid",
		  //				array(':uid' => $owner->drUser->ID)
		  //			); */
		$arrTransactions[] = array(
			(!$this->hdnLocationID ?
					"INSERT INTO `_user_locations` SET"
					. " `CombinedID`='$CombinedID'"
					. ", `UID`=:uid"
					. ", `GeoCountryISO2`=@info_CountryISO2"
					. ", `GeoDivisionCode`=@info_DivisionCombined"
					. ", `GeoCityID`=@info_CityID"
					. ", `UserCountryID`=@info_UserCountryID"
					. ", `UserDivisionID`=@info_UserDivisionID"
					. ", `UserCityID`=@info_UserCityID"
					. ", `Address1`=:addr1"
					. ", `Address2`=:addr2"
					. ", `PostalCode`=:postcode"
					. ", `IsCurrentLocation`=:iscurrent"
					. ", `IsBillingLocation`=:isbilling" :
					"UPDATE `_user_locations` SET"
					. " `UID`=:uid"
					. ", `GeoCountryISO2`=@info_CountryISO2"
					. ", `GeoDivisionCode`=@info_DivisionCombined"
					. ", `GeoCityID`=@info_CityID"
					. ", `UserCountryID`=@info_UserCountryID"
					. ", `UserDivisionID`=@info_UserDivisionID"
					. ", `UserCityID`=@info_UserCityID"
					. ", `Address1`=:addr1"
					. ", `Address2`=:addr2"
					. ", `PostalCode`=:postcode"
					. ", `IsCurrentLocation`=:iscurrent"
					. ", `IsBillingLocation`=:isbilling"
					. " WHERE `CombinedID`=:combid"
			)
			, array(
				':uid' => $owner->drUser->ID,
				':combid' => $CombinedID? : null,
				':adrr1' => $this->txtAddress1? : null,
				':adrr2' => $this->txtAddress2? : null,
				':postcode' => $this->txtPostalCode? : null,
				':iscurrent' => $this->chkIsCurrentLocation? : null,
				':isbilling' => $this->chkIsBillingLocation? : null,
			)
		);
		//Location Contact Cnn
		if ($owner->asa('Info_Contacts') && $owner->hdnContactID) {
			$arrTransactions[] = array(
				"INSERT IGNORE INTO `_user_loc_cntct_cnn`(`LocationsCombinedID`, `ContactCombinedID`)"
				. " VALUES(:locid, :cntctid)"
				, array(
					':locid' => $CombinedID,
					':cntctid' => $owner->hdnContactID,
				)
			);
		}
		$owner->addTransactions($arrTransactions);
	}

}
