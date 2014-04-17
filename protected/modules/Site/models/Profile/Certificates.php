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
		return "hdnCertificateID"
				. ", UID"
				. ", ddlLevel"
				. ", ddlEmploymentType"
				. ", ddlSalaryType"
				. ", ddlWorkCondition"
				. ", chkHealthInsurance"
				. ", chkOvertimePay"
				. ", chkRetirementAccount";
	}

	//----- attrs
	public $hdnCertificateID;
	public $chkHealthInsurance = false;
	public $chkOvertimePay = false;
	public $chkRetirementAccount = false;
	#
	public $ddlLevel;
	public $ddlEmploymentType;
	public $ddlSalaryType;
	public $ddlWorkCondition;
	#
	public $hdnCompanyID;
	public $txtCompanyTitle;
	public $txtCompanyURL;
	public $txtJobTitle;
	public $txtSalaryAmount;
	public $txtTBALayoff;
	public $txtRetirementPercent;
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
				'SQL' => 'SELECT COUNT(*) FROM `_user_experiences` WHERE `CombinedID`=:val',
				'on' => 'Edit, Delete'),
			#
			array('txtCompanyTitle, txtJobTitle', 'required'
				, 'on' => 'Add, Edit'),
			array('txtCompanyURL', 'url',
				'on' => 'Add, Edit'),
			array_merge(array('txtCompanyURL', 'length',
				'on' => 'Add, Edit'), $vl->WebAddress),
			array_merge(array('txtCompanyTitle, txtJobTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
			#
			array('chkHealthInsurance, chkOvertimePay, chkRetirementAccount', 'boolean'
				, 'on' => 'Add, Edit'),
			#
			array_merge(array('txtSalaryAmount', 'numerical'
				, 'on' => 'Add, Edit'), $vl->ExperienceSalaryAmount),
			array_merge(array('txtTBALayoff', 'numerical'
				, 'on' => 'Add, Edit'), $vl->ExperienceTBALayoff),
			array_merge(array('txtRetirementPercent', 'numerical'
				, 'on' => 'Add, Edit'), $vl->ExperienceRetirementAccountPercent),
			#
			array('ddlLevel', 'in'
				, 'range' => array_keys($this->arrLevels)
				, 'on' => 'Add, Edit'),
			array('ddlEmploymentType', 'in'
				, 'range' => array_keys($this->arrEmployTypes)
				, 'on' => 'Add, Edit'),
			array('ddlSalaryType', 'in'
				, 'range' => array_keys($this->arrSalaryTypes)
				, 'on' => 'Add, Edit'),
			array('ddlWorkCondition', 'in'
				, 'range' => array_keys($this->arrWorkConditions)
				, 'on' => 'Add, Edit'),
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
			'chkHealthInsurance' => \t2::Site_User('Health insurance'),
			'chkOvertimePay' => \t2::Site_User('Overtime pay'),
			'chkRetirementAccount' => \t2::Site_User('Retirement account'),
			'ddlLevel' => \t2::Site_User('Level'),
			'ddlEmploymentType' => \t2::Site_User('Employment type'),
			'ddlSalaryType' => \t2::General('Salary type'),
			'ddlWorkCondition' => \t2::Site_User('Work condition'),
			'txtCompanyTitle' => \t2::Site_User('Company title'),
			'txtCompanyURL' => \t2::Site_Company('Company web URL'),
			'txtJobTitle' => \t2::Site_User('Job title'),
			'txtSalaryAmount' => \t2::Site_User('Salary amount'),
			'txtTBALayoff' => \t2::Site_User('Layoff days (TBA)'),
			'txtRetirementPercent' => \t2::Site_User('Retirement percent'),
			#location
			'ddlCountry' => \t2::Site_Common('Country'),
			'ddlDivision' => \t2::Site_Common('Division'),
			'ddlCity' => \t2::Site_Common('City'),
			'txtCountry' => \t2::Site_Common('Country'),
			'txtDivision' => \t2::Site_Common('Division'),
			'txtCity' => \t2::Site_Common('City'),
		);
	}

	public function Save() {
		if (!$this->validate())
			return false;
		$Queries = array();
		if (!$this->hdnCertificateID) {
			$strSQLPart_ID = T\DB::GetNewID_Combined(
							'_user_experiences'
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
		if ($this->txtCompanyURL) {
			$Domain = parse_url($this->txtCompanyURL, PHP_URL_HOST);
			$Domain = ltrim($Domain, 'www.');
		}
		$Queries[] = array(
			!$this->hdnCertificateID ?
					"INSERT INTO `_user_experiences`("
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
					"UPDATE `_user_experiences` SET "
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
				':compid' => $this->hdnCompanyID? : null,
				':compttl' => $this->txtCompanyTitle? : null,
				':compdom' => $Domain? : null,
				':compdom_escaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				':compulr' => $this->txtCompanyURL? : null,
				#
				':jobttl' => $this->txtJobTitle? : null,
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
					\html::ErrMsg_Exit(\t2::Site_Common('Failed! Plz retry.'));
				});
		if ($Result)
			$this->scenario = 'Edit';
		return $Result ? true : false;
	}

	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		$Result = T\DB::Execute("DELETE FROM `_user_experiences` WHERE `CombinedID`=:combid AND `UID`=:uid"
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
							. " FROM `_user_experiences` AS uexp"
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
				'hdnCompanyID' => $dr['CompanyID'],
				'txtJobTitle' => $dr['JobTitle'],
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
				'txtCompanyTitle' => $dr['CompanyTitle'],
				'txtCompanyURL' => $dr['CompanyURL'],
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
