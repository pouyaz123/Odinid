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
 * @property-read array $dtEducations
 * @property-read array $dtFreshEducations
 */
class Educations extends \Base\FormModel {

	const OldestYearLimitation = 75;

	public function getPostName() {
		return "UserEducations";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnEducationID"
				. ", UserID"
				. ", hdnCompanyID"
				. ", ddlLevel"
				. ", ddlEmploymentType"
				. ", ddlSalaryType"
				. ", ddlWorkCondition"
				. ", chkHealthInsurance"
				. ", chkOvertimePay"
				. ", chkRetirementAccount"
				. ", chkToPresent"
				. ", txtFromDate"
				. ", txtToDate";
	}

	//----- attrs
	public $hdnEducationID;
	public $chkHealthInsurance = false;
	public $chkOvertimePay = false;
	public $chkRetirementAccount = false;
	#
	public $ddlLevel;
	public $ddlEmploymentType;
	public $ddlSalaryType;
	public $ddlWorkCondition;
	#
	public $hdnSchoolID;
	public $txtSchoolTitle;
	public $txtSchoolURL;
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
	public $txtFromDate;
	public $txtToDate;
	public $chkToPresent;
	public $txtDescription;
	#
	public $UserID;

	//Education levels
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
			array('hdnEducationID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnEducationID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_experiences` WHERE `CombinedID`=:val',
				'on' => 'Edit, Delete'),
			#
			array('txtFromDate, txtToDate', 'date',
				'format' => C\Regexp::Yii_DateFormat_FullDigit,
				'on' => 'Add, Edit'),
			array('txtFromDate, txtToDate', 'ValidateDate',
				'on' => 'Add, Edit'),
			array_merge(array('txtDescription', 'length',
				'on' => 'Add, Edit'), $vl->Description),
			#
			array('txtSchoolTitle, txtJobTitle', 'required'
				, 'on' => 'Add, Edit'),
			array('txtSchoolURL', 'url',
				'on' => 'Add, Edit'),
			array_merge(array('txtSchoolURL', 'length',
				'on' => 'Add, Edit'), $vl->WebAddress),
			array_merge(array('txtSchoolTitle, txtJobTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
			#
			array('chkHealthInsurance, chkOvertimePay, chkRetirementAccount, chkToPresent', 'boolean'
				, 'on' => 'Add, Edit'),
			#
			array_merge(array('txtSalaryAmount', 'numerical'
				, 'on' => 'Add, Edit'), $vl->EducationSalaryAmount),
			array_merge(array('txtTBALayoff', 'numerical'
				, 'on' => 'Add, Edit'), $vl->EducationTBALayoff),
			array_merge(array('txtRetirementPercent', 'numerical'
				, 'on' => 'Add, Edit'), $vl->EducationRetirementAccountPercent),
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

	public function ValidateDate($attr) {
		if ($this->$attr &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->$attr) &&
				strtotime($this->$attr . ' +0000') > time())
			$this->addError($attr, \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel($attr), '{value}' => $this->$attr)));
	}

	protected function afterValidate() {
		if ($this->txtFromDate && $this->txtToDate &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->txtFromDate) &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->txtToDate) &&
				strtotime($this->txtFromDate . ' +0000') > strtotime($this->txtToDate . ' +0000')) {
			$this->addError('txtFromDate', \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel('txtFromDate'), '{value}' => $this->txtFromDate)));
			$this->addError('txtToDate', \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel('txtToDate'), '{value}' => $this->txtToDate)));
		}
	}

	public function attributeLabels() {
		return array(
			'chkHealthInsurance' => \t2::site_site('Health insurance'),
			'chkOvertimePay' => \t2::site_site('Overtime pay'),
			'chkRetirementAccount' => \t2::site_site('Retirement account'),
			'ddlLevel' => \t2::site_site('Level'),
			'ddlEmploymentType' => \t2::site_site('Employment type'),
			'ddlSalaryType' => \t2::General('Salary type'),
			'ddlWorkCondition' => \t2::site_site('Work condition'),
			'txtSchoolTitle' => \t2::site_site('School title'),
			'txtSchoolURL' => \t2::site_site('Web URL'),
			'txtJobTitle' => \t2::site_site('Job title'),
			'txtSalaryAmount' => \t2::site_site('Salary amount'),
			'txtTBALayoff' => \t2::site_site('Layoff days (TBA)'),
			'txtRetirementPercent' => \t2::site_site('Retirement percent'),
			#location
			'ddlCountry' => \t2::site_site('Country'),
			'ddlDivision' => \t2::site_site('Division'),
			'ddlCity' => \t2::site_site('City'),
			'txtCountry' => \t2::site_site('Country'),
			'txtDivision' => \t2::site_site('Division'),
			'txtCity' => \t2::site_site('City'),
			#
			'txtFromDate' => \t2::site_site('From date'),
			'txtToDate' => \t2::site_site('To date'),
			'chkToPresent' => \t2::site_site('To present'),
			'txtDescription' => \t2::site_site('Description'),
		);
	}

	public function Save() {
		if (!$this->validate())
			return false;
		$Queries = array();
		if (!$this->hdnEducationID) {
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
		if ($this->txtSchoolURL) {
			$Domain = parse_url($this->txtSchoolURL, PHP_URL_HOST);
			$Domain = ltrim($Domain, 'www.');
		}
		$Queries[] = array(
			!$this->hdnEducationID ?
					"INSERT INTO `_user_experiences`("
					. " `CombinedID`, `UID`, `SchoolID`"
					. ", `JobTitle`, `Level`, `EmploymentType`, `SalaryType`, `SalaryAmount`"
					. ", `TBALayoff`, `HealthInsurance`, `OvertimePay`, `WorkCondition`"
					. ", `RetirementAccount`, `RAPercent`"
					. ", `GeoCountryISO2`, `GeoDivisionCode`, `GeoCityID`"
					. ", `UserCountryID`,`UserDivisionID`, `UserCityID`"
					. ", `FromDate`, `ToDate`, `ToPresent`, `Description`)"
					. " VALUE("
					. " @expr_ID:=($strSQLPart_ID), :uid, @expr_SchoolID:=schools_getCreatedSchoolID(:compid, :compttl, :compdom, :compdom_escaped, :compurl)"
					. ", :jobttl, :lvl, :emptype, :saltype, :salamount"
					. ", :tba, :insur, :ovrtpay, :wrkcnd"
					. ", :retaccount, :retpercent"
					. ", @expr_CountryISO2, @expr_DivisionCombined, @expr_CityID"
					. ", @expr_UserCountryID, @expr_UserDivisionID, @expr_UserCityID"
					. ", :frmdt, :todt, :toprsnt, :desc)" :
					"UPDATE `_user_experiences` SET "
					. " `SchoolID`=@expr_SchoolID:=schools_getCreatedSchoolID(:compid, :compttl, :compdom, :compdom_escaped, :compurl)"
					. ", `JobTitle`=:jobttl, `Level`=:lvl, `EmploymentType`=:emptype, `SalaryType`=:saltype, `SalaryAmount`=:salamount"
					. ", `TBALayoff`=:tba, `HealthInsurance`=:insur, `OvertimePay`=:ovrtpay, `WorkCondition`=:wrkcnd"
					. ", `RetirementAccount`=:retaccount, `RAPercent`=:retpercent"
					. ", `GeoCountryISO2`=@expr_CountryISO2, `GeoDivisionCode`=@expr_DivisionCombined, `GeoCityID`=@expr_CityID"
					. ", `UserCountryID`=@expr_UserCountryID, `UserDivisionID`=@expr_UserDivisionID, `UserCityID`=@expr_UserCityID"
					. ", `FromDate`=:frmdt, `ToDate`=:todt, `ToPresent`=:toprsnt, `Description`=:desc"
					. " WHERE `CombinedID`=(@expr_ID:=:combid) AND `UID`=:uid"
			, array(
				':combid' => $this->hdnEducationID,
				':uid' => $this->UserID,
				#
				':compid' => $this->hdnSchoolID? : null,
				':compttl' => $this->txtSchoolTitle? : null,
				':compdom' => $Domain? : null,
				':compdom_escaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				':compurl' => $this->txtSchoolURL? : null,
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
				':frmdt' => $this->txtFromDate? : null,
				':todt' => !$this->chkToPresent && $this->txtToDate ? $this->txtToDate : null,
				':toprsnt' => $this->chkToPresent? : 0,
				':desc' => $this->txtDescription? : null,
			)
		);
		$Result = T\DB::Transaction($Queries, NULL, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::site_site('Failed! Plz retry.'));
				});
		if ($Result) {
			$this->scenario = 'Edit';
			if (!$this->hdnEducationID || !$this->hdnSchoolID) {
				$dr = T\DB::GetRow("SELECT @expr_ID AS CombinedID, @expr_SchoolID AS SchoolID");
				$this->hdnEducationID = $dr['CombinedID'];
				$this->hdnSchoolID = $dr['SchoolID'];
			}
		}
		return $Result ? true : false;
	}

	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		$Result = T\DB::Execute("DELETE FROM `_user_experiences` WHERE `CombinedID`=:combid AND `UID`=:uid"
						, array(
					':combid' => $this->hdnEducationID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtEducations($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			if ($DGP) {
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_experiences` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT uexp.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", IFNULL(gct.`AsciiName`, guct.`City`) AS City"
							. ", ci.`Title` AS SchoolTitle"
							. ", ci.`URL` AS SchoolURL"
							. " FROM `_user_experiences` AS uexp"
							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " uexp.`CombinedID`=:id AND " : '') . " UID=:uid"
							. " LEFT JOIN `_universityschool_info` AS ci ON ci.`ID`=uexp.SchoolID"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=uexp.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=uexp.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =uexp.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=uexp.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=uexp.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=uexp.`UserCityID`"
							. ($DGP ?
									"  WHERE {$DGP->SQLWhereClause}"
									. "  ORDER BY {$DGP->Sort}"
									. "  LIMIT $Limit" : "")
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
	public function getdtFreshEducations($ID = null) {
		static $R = null;
		if (!$R)
			$R = $this->getdtEducations($ID, true);
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtEducations($this->hdnEducationID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnEducationID' => $dr['CombinedID'],
				'hdnSchoolID' => $dr['SchoolID'],
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
				'txtSchoolTitle' => $dr['SchoolTitle'],
				'txtSchoolURL' => $dr['SchoolURL'],
				#
				'ddlCountry' => $dr['GeoCountryISO2'] ? : ($dr['Country'] ? '_other_' : ''),
				'ddlDivision' => $dr['GeoDivisionCode'] ? : ($dr['Division'] ? '_other_' : ''),
				'ddlCity' => $dr['GeoCityID'] ? : ($dr['City'] ? '_other_' : ''),
				'txtCountry' => $dr['GeoCountryISO2'] ? : $dr['Country'],
				'txtDivision' => $dr['GeoDivisionCode'] ? : $dr['Division'],
				'txtCity' => $dr['GeoCityID'] ? : $dr['City'],
				'txtFromDate' => $dr['FromDate'],
				'txtToDate' => $dr['ToDate'],
				'chkToPresent' => $dr['ToPresent'],
				'txtDescription' => $dr['Description'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
