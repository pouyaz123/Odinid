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
 * @mytodo 1 : Expreinces has optional location items (add them to locations)
 */
class Experiences extends \Base\FormModel {

	public function getPostName() {
		return "UserExperiences";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnCombinedID"
				. ", UID"
				. ", ddlLevel"
				. ", ddlEmploymentType"
				. ", ddlSalaryType"
				. ", chkHealthInsurance"
				. ", chkOvertimePay"
				. ", ddlWorkCondition"
				. ", chkRetirementAccount";
	}

	//----- attrs
	public $hdnCombinedID;
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
	public $UID;

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

	public function rules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnCombinedID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnCombinedID', 'IsExist',
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
		$CommonParams = array(
			':un' => $this->txtUsername,
		);
			$strSQLPart_ExpID = T\DB::GetNewID_Combined(
							'_user_experiences'
							, 'CombinedID'
							, 'UID=@regstr_uid'
							, null
							, array('PrefixQuery' => "CONCAT(@regstr_uid, '_')"));
		$Domain = parse_url($this->txtCompanyURL, PHP_URL_HOST);
		$Domain = ltrim($Domain, 'www.');
		$Queries[] = array(
			"INSERT INTO `_user_experiences`()"
			. "CALL companies_getCreatedCompanyID(:hdnCompanyID, :companyTitle, :companyDomain, :companyDomainLikeEscaped)"
			, array(
				':companyTitle' => $this->txtCompanyTitle,
				':companyDomain' => $Domain,
				':companyDomainLikeEscaped' => T\DB::EscapeLikeWildCards($Domain),
				':hdnCompanyID' => $this->hdnCompanyID,
			)
		);
		//locations
		$Queries[] = array(
			"CALL geo_getGeoLocationIDs(
				:country
				, :division
				, :city
				, @regstr_CountryISO2
				, @regstr_DivisionCombined
				, @regstr_DivisionCode
				, @regstr_CityID);
			CALL geo_getUserLocationIDs(
				:country
				, :division
				, :city
				, @regstr_CountryISO2
				, @regstr_DivisionCombined
				, @regstr_CityID
				, @regstr_UserCountryID
				, @regstr_UserDivisionID
				, @regstr_UserCityID)"
			, array(
				':country' => $this->txtCountry? : $this->ddlCountry,
				':division' => $this->txtDivision? : $this->ddlDivision,
				':city' => $this->txtCity? : $this->ddlCity,
			)
		);
		$Result = T\DB::Transaction($Queries, $CommonParams, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::Site_Common('Failed! Plz retry.'));
				});
		return $Result ? true : false;
	}

//	public $hdnCombinedID;
//	public $chkHealthInsurance = false;
//	public $chkOvertimePay = false;
//	public $chkRetirementAccount = false;
//	#
//	public $ddlLevel;
//	public $ddlEmploymentType;
//	public $ddlSalaryType;
//	public $ddlWorkCondition;
//	#
//	public $txtCompanyTitle;
//	public $txtJobTitle;
//	public $txtSalaryAmount;
//	public $txtTBALayoff;
//	public $txtRetirementPercent;
}
