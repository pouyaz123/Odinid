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
 * @property-read array $arrLevels
 * @property-read array $arrEmployTypes
 * @property-read array $arrSalaryTypes
 * @property-read array $arrWorkConditions
 * @property-read array $dtExperiences
 * @property-read array $dtFreshExperiences
 * @mytodo 1 : Expreinces has optional location items (add them to locations)
 */
class Experiences extends \Base\FormModel {

	public function getPostName() {
		return "UserExperiences";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnExperienceID"
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
	public $hdnExperienceID;
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

	//Experience levels
	const Level_Junior = 'Junior';
	const Level_Senior = 'Senior';
	const Level_Leader = 'Leader';

	function getarrLevels() {
		return array(
			self::Level_Junior => self::Level_Junior,
			self::Level_Senior => self::Level_Senior,
			self::Level_Leader => self::Level_Leader,
		);
	}

	//Employment Types
	const EmployType_Staff = 'Staff';
	const EmployType_Freelance = 'Freelance';

	function getarrEmployTypes() {
		return array(
			self::EmployType_Staff => self::EmployType_Staff,
			self::EmployType_Freelance => self::EmployType_Freelance,
		);
	}

	//Salary Types
	const SalaryType_Monthly = 'Monthly';
	const SalaryType_Biweekly = 'Biweekly';
	const SalaryType_Weekly = 'Weekly';
	const SalaryType_Daily = 'Daily';
	const SalaryType_Hourly = 'Hourly';

	function getarrSalaryTypes() {
		return array(
			self::SalaryType_Monthly => self::SalaryType_Monthly,
			self::SalaryType_Biweekly => self::SalaryType_Biweekly,
			self::SalaryType_Weekly => self::SalaryType_Weekly,
			self::SalaryType_Daily => self::SalaryType_Daily,
			self::SalaryType_Hourly => self::SalaryType_Hourly,
		);
	}

	//Work Conditions
	const WorkCondition_Sweatshop = 'Sweatshop';
	const WorkCondition_Ok = 'Ok';
	const WorkCondition_Good = 'Good';
	const WorkCondition_Excellent = 'Excellent';

	function getarrWorkConditions() {
		return array(
			self::WorkCondition_Sweatshop => self::WorkCondition_Sweatshop,
			self::WorkCondition_Ok => self::WorkCondition_Ok,
			self::WorkCondition_Good => self::WorkCondition_Good,
			self::WorkCondition_Excellent => self::WorkCondition_Excellent,
		);
	}

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnExperienceID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnExperienceID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_experiences` WHERE `CombinedID`=:val',
				'on' => 'Edit, Delete'),
			#
			array('txtCompanyTitle, txtJobTitle, hdnCompanyID', 'required'
				, 'except' => 'Delete'),
			array('txtCompanyURL', 'url',
				'except' => 'Delete'),
			array_merge(array('txtCompanyURL', 'length',
				'except' => 'Delete'), $vl->WebAddress),
			array_merge(array('txtCompanyTitle, txtJobTitle', 'length'
				, 'except' => 'Delete'), $vl->Title),
			#
			array('chkHealthInsurance, chkOvertimePay, chkRetirementAccount', 'boolean'
				, 'except' => 'Delete'),
			#
			array_merge(array('txtSalaryAmount', 'numerical'
				, 'except' => 'Delete'), $vl->ExperienceSalaryAmount),
			array_merge(array('txtTBALayoff', 'numerical'
				, 'except' => 'Delete'), $vl->ExperienceTBALayoff),
			array_merge(array('txtRetirementPercent', 'numerical'
				, 'except' => 'Delete'), $vl->ExperienceRetirementAccountPercent),
			#
			array('ddlLevel', 'in'
				, 'range' => array_keys($this->arrLevels)
				, 'except' => 'Delete'),
			array('ddlEmploymentType', 'in'
				, 'range' => array_keys($this->arrEmployTypes)
				, 'except' => 'Delete'),
			array('ddlSalaryType', 'in'
				, 'range' => array_keys($this->arrSalaryTypes)
				, 'except' => 'Delete'),
			array('ddlWorkCondition', 'in'
				, 'range' => array_keys($this->arrWorkConditions)
				, 'except' => 'Delete'),
			#location
			array('ddlCountry, ddlDivision, ddlCity, txtCountry, txtDivision, txtCity', 'match',
				'pattern' => C\Regexp::SimpleWords,
				'except' => 'Delete'),
			array_merge(array('ddlCountry, txtCountry', 'length',
				'except' => 'Delete'), $vl->Country),
			array_merge(array('ddlDivision, txtDivision', 'length',
				'except' => 'Delete'), $vl->Division),
			array_merge(array('ddlCity, txtCity', 'length',
				'except' => 'Delete'), $vl->City),
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

//mytodo 1 : complete the experiences save and delete
	public function Save() {
		if (!$this->validate())
			return false;
		$Queries = array();
		if ($this->hdnExperienceID) {
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
		$Domain = parse_url($this->txtCompanyURL, PHP_URL_HOST);
		$Domain = ltrim($Domain, 'www.');
		$Queries[] = array(
			!$this->hdnExperienceID ?
					"INSERT INTO `_user_experiences`("
					. " `CombinedID`, `UID`, `CompanyID`"
					. ", `JobTitle`, `Level`, `EmploymentType`, `SalaryType`, `SalaryAmount`"
					. ", `TBALayoff`, `HealthInsurance`, `OvertimePay`, `WorkCondition`"
					. ", `RetirementAccount`, `RAPercent`"
					. ", `GeoCountryISO2`, `GeoDivisionCode`, `GeoCityID`"
					. ", `UserCountryID`,`UserDivisionID`, `UserCityID`)"
					. " VALUE("
					. "($strSQLPart_ID), :uid, companies_getCreatedCompanyID(:compid, :compttl, :compdom, :compdom_escaped)"
					. ", :jobttl, :lvl, :emptype, :saltype, :salamount"
					. ", :tba, :insur, :ovrtpay, :wrkcnd"
					. ", :retaccount, :retpercent"
					. ", @expr_CountryISO2, @expr_DivisionCombined, @expr_CityID"
					. ", @expr_UserCountryID, @expr_UserDivisionID, @expr_UserCityID)" :
					"UPDATE `_user_experiences` SET "
					. " `CompanyID`=:compid"
					. ", `JobTitle`=:jobttl, `Level`=:lvl, `EmploymentType`=:emptype, `SalaryType`=:saltype, `SalaryAmount`=:salamount"
					. ", `TBALayoff`=:tba, `HealthInsurance`=:insur, `OvertimePay`=:ovrtpay, `WorkCondition`=:wrkcnd"
					. ", `RetirementAccount`=:retaccount, `RAPercent`=:retpercent"
					. ", `GeoCountryISO2`=@expr_CountryISO2, `GeoDivisionCode`=@expr_DivisionCombined, `GeoCityID`=@expr_CityID"
					. ", `UserCountryID`=@expr_UserCountryID, `UserDivisionID`=@expr_UserDivisionID, `UserCityID`=@expr_UserCityID"
					. " WHERE `CombinedID`=:combid AND `UID`=:uid"
			, array(
				':combid' => $this->hdnExperienceID,
				':uid' => $this->UserID,
				#
				':compid' => $this->hdnCompanyID,
				':compttl' => $this->txtCompanyTitle,
				':compdom' => $Domain,
				':compdom_escaped' => T\DB::EscapeLikeWildCards($Domain),
				#
				':jobttl' => $this->txtJobTitle,
				':lvl' => $this->ddlLevel,
				':emptype' => $this->ddlEmploymentType,
				':saltype' => $this->ddlSalaryType,
				':salamount' => $this->txtSalaryAmount,
				':tba' => $this->txtTBALayoff,
				':insur' => $this->chkHealthInsurance,
				':ovrtpay' => $this->chkOvertimePay,
				':wrkcnd' => $this->ddlWorkCondition,
				':retaccount' => $this->chkRetirementAccount,
				':retpercent' => $this->txtRetirementPercent,
			)
		);
		$Result = T\DB::Transaction($Queries, $CommonParams, function(\Exception $ex) {
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
					':combid' => $this->hdnExperienceID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtExperiences($ID = NULL, $refresh = false) {
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
//							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " uexp.`CombinedID`=:id AND " : '') . " UID=:uid"
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
	public function getdtFreshExperiences($ID = null) {
		static $R = null;
		if (!$R)
			$R = $this->getdtExperiences($ID, true);
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtExperiences($this->hdnExperienceID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnExperienceID' => $dr['CombinedID'],
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
				'ddlCountry' => $dr['GeoCountryISO2']? : '_other_',
				'ddlDivision' => $dr['GeoDivisionCode']? : '_other_',
				'ddlCity' => $dr['GeoCityID']? : '_other_',
				'txtCountry' => $dr['GeoCountryISO2'] ? : $dr['Country'],
				'txtDivision' => $dr['GeoDivisionCode'] ? : $dr['Division'],
				'txtCity' => $dr['GeoCityID'] ? : $dr['City'],
			);
			$this->attributes = $arrAttrs;
		}
	}
}
