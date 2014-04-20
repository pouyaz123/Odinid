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
 * @property-read array $dtCertificates
 * @property-read array $dtFreshCertificates
 */
class Certificates extends \Base\FormModel {

	public function getPostName() {
		return "UserCertificates";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnCertificateID";
	}

	//----- attrs
	public $hdnCertificateID;
	public $txtTitle;
	#
	public $hdnInstitutionID;
	public $txtInstitutionTitle;
	public $txtInstitutionURL;
	//geolocations
	public $ddlCountry;
	public $ddlDivision;
	public $ddlCity;
	public $txtCountry;
	public $txtDivision;
	public $txtCity;
	#
	public $UserID;

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnCertificateID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnCertificateID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_certificates` WHERE `CombinedID`=:val',
				'on' => 'Edit, Delete'),
			#
			array('txtInstitutionTitle, txtTitle', 'required'
				, 'on' => 'Add, Edit'),
			array('txtInstitutionURL', 'url',
				'on' => 'Add, Edit'),
			array_merge(array('txtInstitutionURL', 'length',
				'on' => 'Add, Edit'), $vl->WebAddress),
			array_merge(array('txtInstitutionTitle, txtTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
			#location
			array('ddlCountry, ddlDivision, ddlCity, txtCountry, txtDivision, txtCity', 'match',
				'pattern' => C\Regexp::SimpleWords,
				'on' => 'Add, Edit'),
			array_merge(array('ddlCountry, txtCountry', 'length',
				'on' => 'Add, Edit'), $vl->Country),
			array_merge(array('ddlDivision, txtDivision', 'length',
				'on' => 'Add, Edit'), $vl->Division),
			array_merge(array('ddlCity, txtCity', 'length',
				'on' => 'Add, Edit'), $vl->City),
		);
	}

	public function attributeLabels() {
		return array(
			'txtTitle' => \t2::site_site('Title'),
			'txtInstitutionTitle' => \t2::site_site('Institution title'),
			'txtInstitutionURL' => \t2::site_site('Company web URL'),
			#location
			'ddlCountry' => \t2::site_site('Country'),
			'ddlDivision' => \t2::site_site('Division'),
			'ddlCity' => \t2::site_site('City'),
			'txtCountry' => \t2::site_site('Country'),
			'txtDivision' => \t2::site_site('Division'),
			'txtCity' => \t2::site_site('City'),
		);
	}

	public function Save() {
		if (!$this->validate())
			return false;
		$Queries = array();
		if (!$this->hdnCertificateID) {
			$strSQLPart_ID = T\DB::GetNewID_Combined(
							'_user_certificates'
							, 'CombinedID'
							, 'UID=:uid'
							, array($this->UserID)
							, array('PrefixQuery' => "CONCAT(:uid, '_')"));
		}
		//locations
		$Queries[] = array(
			"CALL geo_getGeoLocationIDs(
				:country
				, :division
				, :city
				, @expr_CountryISO2
				, @expr_DivisionCombined
				, @expr_DivisionCode
				, @expr_CityID);
			CALL geo_getUserLocationIDs(
				:country
				, :division
				, :city
				, @expr_CountryISO2
				, @expr_DivisionCombined
				, @expr_CityID
				, @expr_UserCountryID
				, @expr_UserDivisionID
				, @expr_UserCityID)"
			, array(
				':country' => ($this->txtCountry? : $this->ddlCountry)? : NULL,
				':division' => ($this->txtDivision? : $this->ddlDivision)? : NULL,
				':city' => ($this->txtCity? : $this->ddlCity)? : NULL,
			)
		);
		$Domain = '';
		if ($this->txtInstitutionURL) {
			$Domain = parse_url($this->txtInstitutionURL, PHP_URL_HOST);
			$Domain = ltrim($Domain, 'www.');
		}
		$Queries[] = array(
			!$this->hdnCertificateID ?
					"INSERT INTO `_user_certificates`("
					. " `CombinedID`, `UID`, `CompanyID`"
					. ", `JobTitle`, `Level`, `EmploymentType`, `SalaryType`, `SalaryAmount`"
					. ", `TBALayoff`, `HealthInsurance`, `OvertimePay`, `WorkCondition`"
					. ", `RetirementAccount`, `RAPercent`"
					. ", `GeoCountryISO2`, `GeoDivisionCode`, `GeoCityID`"
					. ", `UserCountryID`,`UserDivisionID`, `UserCityID`)"
					. " VALUE("
					. "($strSQLPart_ID), :uid, companies_getCreatedCompanyID(:compid, :compttl, :compdom, :compdom_escaped, :compulr)"
					. ", :jobttl, :lvl, :emptype, :saltype, :salamount"
					. ", :tba, :insur, :ovrtpay, :wrkcnd"
					. ", :retaccount, :retpercent"
					. ", @expr_CountryISO2, @expr_DivisionCombined, @expr_CityID"
					. ", @expr_UserCountryID, @expr_UserDivisionID, @expr_UserCityID)" :
					"UPDATE `_user_certificates` SET "
					. " `CompanyID`=companies_getCreatedCompanyID(:compid, :compttl, :compdom, :compdom_escaped, :compulr)"
					. ", `JobTitle`=:jobttl, `Level`=:lvl, `EmploymentType`=:emptype, `SalaryType`=:saltype, `SalaryAmount`=:salamount"
					. ", `TBALayoff`=:tba, `HealthInsurance`=:insur, `OvertimePay`=:ovrtpay, `WorkCondition`=:wrkcnd"
					. ", `RetirementAccount`=:retaccount, `RAPercent`=:retpercent"
					. ", `GeoCountryISO2`=@expr_CountryISO2, `GeoDivisionCode`=@expr_DivisionCombined, `GeoCityID`=@expr_CityID"
					. ", `UserCountryID`=@expr_UserCountryID, `UserDivisionID`=@expr_UserDivisionID, `UserCityID`=@expr_UserCityID"
					. " WHERE `CombinedID`=:combid AND `UID`=:uid"
			, array(
				':combid' => $this->hdnCertificateID,
				':uid' => $this->UserID,
				#
				':compid' => $this->hdnInstitutionID? : null,
				':compttl' => $this->txtInstitutionTitle? : null,
				':compdom' => $Domain? : null,
				':compdom_escaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				':compulr' => $this->txtInstitutionURL? : null,
				#
				':jobttl' => $this->txtTitle? : null,
				':lvl' => $this->ddlLevel? : null,
				':emptype' => $this->ddlEmploymentType? : null,
				':saltype' => $this->ddlSalaryType? : null,
				':salamount' => $this->txtSalaryAmount? : null,
				':tba' => $this->txtTBALayoff? : null,
				':insur' => $this->chkHealthInsurance,
				':ovrtpay' => $this->chkOvertimePay,
				':wrkcnd' => $this->ddlWorkCondition? : null,
				':retaccount' => $this->chkRetirementAccount,
				':retpercent' => $this->txtRetirementPercent? : null,
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
		$Result = T\DB::Execute("DELETE FROM `_user_certificates` WHERE `CombinedID`=:combid AND `UID`=:uid"
						, array(
					':combid' => $this->hdnCertificateID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtCertificates($ID = NULL, $refresh = false) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT uexp.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", IFNULL(gct.`AsciiName`, guct.`City`) AS City"
							. ", ci.`Title` AS CompanyTitle"
							. ", ci.`URL` AS CompanyURL"
							. " FROM `_user_certificates` AS uexp"
							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " uexp.`CombinedID`=:id AND " : '') . " UID=:uid"
							. " LEFT JOIN `_company_info` AS ci ON ci.`ID`=uexp.CompanyID"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=uexp.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=uexp.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =uexp.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=uexp.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=uexp.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=uexp.`UserCityID`"
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
	public function getdtFreshCertificates($ID = null) {
		static $R = null;
		if (!$R)
			$R = $this->getdtCertificates($ID, true);
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtCertificates($this->hdnCertificateID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnCertificateID' => $dr['CombinedID'],
				'hdnInstitutionID' => $dr['CompanyID'],
				'txtTitle' => $dr['JobTitle'],
				'ddlLevel' => $dr['Level'],
				'ddlEmploymentType' => $dr['EmploymentType'],
				'ddlSalaryType' => $dr['SalaryType'],
				'txtSalaryAmount' => $dr['SalaryAmount'],
				'txtTBALayoff' => $dr['TBALayoff'],
				'chkHealthInsurance' => $dr['HealthInsurance'],
				'chkOvertimePay' => $dr['OvertimePay'],
				'ddlWorkCondition' => $dr['WorkCondition'],
				'chkRetirementAccount' => $dr['RetirementAccount'],
				'txtRetirementPercent' => $dr['RAPercent'],
				#
				'txtInstitutionTitle' => $dr['CompanyTitle'],
				'txtInstitutionURL' => $dr['CompanyURL'],
				#
				'ddlCountry' => $dr['GeoCountryISO2'] ? : ($dr['Country'] ? '_other_' : ''),
				'ddlDivision' => $dr['GeoDivisionCode'] ? : ($dr['Division'] ? '_other_' : ''),
				'ddlCity' => $dr['GeoCityID'] ? : ($dr['City'] ? '_other_' : ''),
				'txtCountry' => $dr['GeoCountryISO2'] ? : $dr['Country'],
				'txtDivision' => $dr['GeoDivisionCode'] ? : $dr['Division'],
				'txtCity' => $dr['GeoCityID'] ? : $dr['City'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
