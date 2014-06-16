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
		$owner = $this->owner;
		if (!$this->hdnLocationID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_user_locations` WHERE `UID`=:uid"
							, array(':uid' => $owner->drUser['ID']));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxUserLocations)
				$owner->addError('', \t2::site_site('You have reached the maximum'));
		}
		if (!$owner->hasErrors()) {
			foreach ($this->dtLocations as $dr) {
				if ($this->hdnLocationID && $dr['CombinedID'] === $this->hdnLocationID)
					continue;
				if (
						($dr['GeoCountryISO2'] == $this->ddlCountry || $dr['Country'] == $this->txtCountry) &&
						($dr['GeoDivisionCode'] == $this->ddlDivision || $dr['Division'] == $this->txtDivision) &&
						($dr['GeoCityID'] == $this->ddlCity || $dr['City'] == $this->txtCity) &&
						$dr['Address1'] == $this->txtAddress1 && $dr['Address2'] == $this->txtAddress2
				)
					$this->owner->addError('', \t2::site_site('This geographical location has been used previously'));
			}
		}
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'ddlCountry' => \t2::site_site('Country'),
			'ddlDivision' => \t2::site_site('Division'),
			'ddlCity' => \t2::site_site('City'),
			'txtCountry' => \t2::site_site('Country'),
			'txtDivision' => \t2::site_site('Division'),
			'txtCity' => \t2::site_site('City'),
			'txtAddress1' => \t2::site_site('Address 1'),
			'txtAddress2' => \t2::site_site('Address 2'),
			'txtPostalCode' => \t2::site_site('Postal code'),
			'chkIsCurrentLocation' => \t2::site_site('Is current location'),
			'chkIsBillingLocation' => \t2::site_site('Is billing location'),
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

	public function getdtLocations($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = null;
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			if ($DGP) {
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_locations` WHERE `UID`=:uid'
								, array(':uid' => $this->owner->drUser->ID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			//mytodo x: in Info_Locations i think we can query faster if we put these joins in a procedure
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT "
							. " locs.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", IFNULL(gct.`AsciiName`, guct.`City`) AS City"
							. " FROM `_user_locations` AS locs"
							. " INNER JOIN (SELECT 1) AS tmp ON " . ($ID ? " locs.CombinedID=:id AND " : "") . " locs.`UID`=:uid"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=locs.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=locs.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =locs.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=locs.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=locs.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=locs.`UserCityID`"
							. ($DGP ?
									" WHERE {$DGP->SQLWhereClause}"
									. " ORDER BY {$DGP->Sort}"
									. " LIMIT $Limit" : "")
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
	public function getdtFreshLocations($ID = null, \Base\DataGridParams $DGP = NULL) {
		static $F = true;
		$R = $this->getdtLocations($ID, $F);
		$F = false;
		return $R;
	}

	public function onDelete(\CEvent $e) {
		$this->raiseEvent('onDelete', $e);
		$this->owner->addTransactions(array(
			array(
				"DELETE FROM `_user_locations` WHERE `CombinedID`=:combid AND `UID`=:uid",
				array(
					':uid' => $this->owner->drUser->ID,
					':combid' => $this->hdnLocationID,
				)
			)
		));
	}

	//remember : you can't reuse an instance of this behavior in a model for multiple add/edit because each time it should be validated before adding new transaction
	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		$CombinedID = $this->hdnLocationID? : T\DB::GetNewID_Combined(
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
					, @location_CountryISO2
					, @location_DivisionCombined
					, @location_DivisionCode
					, @location_CityID);
				CALL geo_getUserLocationIDs(
					:country
					, :division
					, :city
					, @location_CountryISO2
					, @location_DivisionCombined
					, @location_CityID
					, @location_UserCountryID
					, @location_UserDivisionID
					, @location_UserCityID)"
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
		if ($this->chkIsCurrentLocation || $this->chkIsBillingLocation) {
			$arrTransactions[] = array(
				"UPDATE `_user_locations` SET "
				. ($this->chkIsCurrentLocation ? " `IsCurrentLocation`=NULL " : "")
				. ($this->chkIsCurrentLocation && $this->chkIsBillingLocation ? " , " : "")
				. ($this->chkIsBillingLocation ? " `IsBillingLocation`=NULL " : "")
				. " WHERE UID=:uid AND ( "
				. ($this->chkIsCurrentLocation ? " NOT ISNULL(`IsCurrentLocation`) " : "")
				. ($this->chkIsCurrentLocation && $this->chkIsBillingLocation ? " OR " : "")
				. ($this->chkIsBillingLocation ? " NOT ISNULL(`IsBillingLocation`) " : "")
				. " )"
				, array(':uid' => $owner->drUser['ID'])
			);
		}
		$arrTransactions[] = array(
			(!$this->hdnLocationID ?
					"INSERT INTO `_user_locations` SET"
					. " `CombinedID`='$CombinedID'"
					. ", `UID`=:uid"
					. ", `GeoCountryISO2`=@location_CountryISO2"
					. ", `GeoDivisionCode`=@location_DivisionCombined"
					. ", `GeoCityID`=@location_CityID"
					. ", `UserCountryID`=@location_UserCountryID"
					. ", `UserDivisionID`=@location_UserDivisionID"
					. ", `UserCityID`=@location_UserCityID"
					. ", `Address1`=:addr1"
					. ", `Address2`=:addr2"
					. ", `PostalCode`=:postcode"
					. ", `IsCurrentLocation`=:iscurrent"
					. ", `IsBillingLocation`=:isbilling" :
					"UPDATE `_user_locations` SET"
					. " `UID`=:uid"
					. ", `GeoCountryISO2`=@location_CountryISO2"
					. ", `GeoDivisionCode`=@location_DivisionCombined"
					. ", `GeoCityID`=@location_CityID"
					. ", `UserCountryID`=@location_UserCountryID"
					. ", `UserDivisionID`=@location_UserDivisionID"
					. ", `UserCityID`=@location_UserCityID"
					. ", `Address1`=:addr1"
					. ", `Address2`=:addr2"
					. ", `PostalCode`=:postcode"
					. ", `IsCurrentLocation`=:iscurrent"
					. ", `IsBillingLocation`=:isbilling"
					. " WHERE `CombinedID`=:combid AND `UID`=:uid"
			)
			, array(
				':uid' => $owner->drUser->ID,
				':combid' => $CombinedID? : null,
				':addr1' => $this->txtAddress1? : null,
				':addr2' => $this->txtAddress2? : null,
				':postcode' => $this->txtPostalCode? : null,
				':iscurrent' => $this->chkIsCurrentLocation? : null,
				':isbilling' => $this->chkIsBillingLocation? : null,
			)
		);
		//Location Phone Cnn
//		if ($owner->asa('Info_Contacts') && $owner->hdnContactID) {
//			$arrTransactions[] = array(
//				"INSERT IGNORE INTO `_user_loc_cntct_cnn`(`LocationID`, `ContactID`)"
//				. " VALUES(:locid, :cntctid)"
//				, array(
//					':locid' => $CombinedID,
//					':cntctid' => $owner->hdnContactID,
//				)
//			);
//		}
		if (!$this->hdnLocationID)
			$this->hdnLocationID = $CombinedID;
		$owner->addTransactions($arrTransactions);
	}

	public function onSetForm(\CEvent $e) {
		$this->raiseEvent('onSetForm', $e);
		$owner = $this->owner;
		$dr = $this->getdtLocations($this->hdnLocationID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnLocationID' => $dr['CombinedID'],
				'ddlCountry' => $dr['GeoCountryISO2']? : '_other_',
				'ddlDivision' => $dr['GeoDivisionCode']? : '_other_',
				'ddlCity' => $dr['GeoCityID']? : '_other_',
				'txtCountry' => $dr['GeoCountryISO2'] ? : $dr['Country'],
				'txtDivision' => $dr['GeoDivisionCode'] ? : $dr['Division'],
				'txtCity' => $dr['GeoCityID'] ? : $dr['City'],
				'txtAddress1' => $dr['Address1'],
				'txtAddress2' => $dr['Address2'],
				'txtPostalCode' => $dr['PostalCode'],
				'chkIsCurrentLocation' => $dr['IsCurrentLocation'],
				'chkIsBillingLocation' => $dr['IsBillingLocation'],
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
