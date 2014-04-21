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
 * @property-read array $dtAwards
 * @property-read array $dtFreshAwards
 */
class Awards extends \Base\FormModel {

	const OldestYearLimitation = 50;

	public function getPostName() {
		return "UserAwards";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnAwardID";
	}

	//----- attrs
	public $hdnAwardID;
	public $txtTitle;
	public $ddlYear;
	public $txtDescription;
	#
	public $hdnOrganizationID;
	public $txtOrganizationTitle;
	public $txtOrganizationURL;
	#
	public $UserID;

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnAwardID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnAwardID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_awards` WHERE `CombinedID`=:val',
				'on' => 'Edit, Delete'),
			#
			array('txtOrganizationTitle, txtTitle', 'required'
				, 'on' => 'Add, Edit'),
			array('txtOrganizationURL', 'url',
				'on' => 'Add, Edit'),
			array_merge(array('txtOrganizationURL', 'length',
				'on' => 'Add, Edit'), $vl->WebAddress),
			array_merge(array('txtOrganizationTitle, txtTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
			array('ddlYear', 'ValidateYear',
				'on' => 'Add, Edit'),
			array_merge(array('txtDescription', 'length',
				'on' => 'Add, Edit'), $vl->Description),
		);
	}

	public function ValidateYear($attr) {
		if ($this->$attr &&
				(!preg_match(C\Regexp::YearFormat_FullDigit, $this->$attr) ||
				$this->$attr > gmdate('Y'))
		)
			$this->addError($attr, \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel($attr), '{value}' => $this->$attr)));
	}

	public function attributeLabels() {
		return array(
			'txtTitle' => \t2::site_site('Title'),
			'txtDescription' => \t2::site_site('Description'),
			'ddlYear' => \t2::site_site('Date'),
			'txtOrganizationTitle' => \t2::site_site('Organization title'),
			'txtOrganizationURL' => \t2::site_site('Web URL'),
		);
	}

	public function Save() {
		if (!$this->validate())
			return false;
		$Queries = array();
		if (!$this->hdnAwardID) {
			$strSQLPart_ID = T\DB::GetNewID_Combined(
							'_user_awards'
							, 'CombinedID'
							, 'UID=:uid'
							, array($this->UserID)
							, array('PrefixQuery' => "CONCAT(:uid, '_')"));
		}
		$Domain = '';
		if ($this->txtOrganizationURL) {
			$Domain = parse_url($this->txtOrganizationURL, PHP_URL_HOST);
			$Domain = ltrim($Domain, 'www.');
		}
		$Queries[] = array(
			!$this->hdnAwardID ?
					"INSERT INTO `_user_awards`("
					. " `CombinedID`, `UID`, `OrganizationID`"
					. ", `Title`, `Date`, `Description`"
					. ", `GeoCountryISO2`, `GeoDivisionCode`, `GeoCityID`"
					. ", `UserCountryID`,`UserDivisionID`, `UserCityID`)"
					. " VALUE("
					. " ($strSQLPart_ID), :uid, organizations_getCreatedOrganizationID(:instid, :instttl, :instdom, :instdom_escaped, :instulr)"
					. ", :ttl, :date, :desc"
					. ", @cert_CountryISO2, @cert_DivisionCombined, @cert_CityID"
					. ", @cert_UserCountryID, @cert_UserDivisionID, @cert_UserCityID)" :
					"UPDATE `_user_awards` SET "
					. " `OrganizationID`=organizations_getCreatedOrganizationID(:instid, :instttl, :instdom, :instdom_escaped, :instulr)"
					. ", `Title`=:ttl, `Date`=:date, `Description`:desc"
					. ", `GeoCountryISO2`=@cert_CountryISO2, `GeoDivisionCode`=@cert_DivisionCombined, `GeoCityID`=@cert_CityID"
					. ", `UserCountryID`=@cert_UserCountryID, `UserDivisionID`=@cert_UserDivisionID, `UserCityID`=@cert_UserCityID"
					. " WHERE `CombinedID`=:combid AND `UID`=:uid"
			, array(
				':combid' => $this->hdnAwardID,
				':uid' => $this->UserID,
				#
				':instid' => $this->hdnOrganizationID? : null,
				':instttl' => $this->txtOrganizationTitle? : null,
				':instdom' => $Domain? : null,
				':instdom_escaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				':instulr' => $this->txtOrganizationURL? : null,
				#
				':ttl' => $this->txtTitle? : null,
				':date' => $this->ddlYear? : null,
				':desc' => $this->txtDescription? : null,
			)
		);
		$Result = T\DB::Transaction($Queries, NULL, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::site_site('Failed! Plz retry.'));
				});
		if ($Result)
			$this->scenario = 'Edit';
		return $Result ? true : false;
	}

	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		$Result = T\DB::Execute("DELETE FROM `_user_awards` WHERE `CombinedID`=:combid AND `UID`=:uid"
						, array(
					':combid' => $this->hdnAwardID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtAwards($ID = NULL, $refresh = false) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT ucert.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", IFNULL(gct.`AsciiName`, guct.`City`) AS City"
							. ", ins.`Title` AS OrganizationTitle"
							. ", ins.`URL` AS OrganizationURL"
							. " FROM `_user_awards` AS ucert"
							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " ucert.`CombinedID`=:id AND " : '') . " UID=:uid"
							. " LEFT JOIN `_organizations` AS ins ON ins.`ID`=ucert.OrganizationID"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=ucert.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=ucert.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =ucert.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=ucert.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=ucert.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=ucert.`UserCityID`"
							, array(
						':uid' => $this->UserID,
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
	public function getdtFreshAwards($ID = null) {
		static $R = null;
		if (!$R)
			$R = $this->getdtAwards($ID, true);
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtAwards($this->hdnAwardID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnAwardID' => $dr['CombinedID'],
				'hdnOrganizationID' => $dr['OrganizationID'],
				'txtTitle' => $dr['Title'],
				'ddlYear' => $dr['Date'],
				'txtDescription' => $dr['Description'],
				#
				'txtOrganizationTitle' => $dr['OrganizationTitle'],
				'txtOrganizationURL' => $dr['OrganizationURL'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
