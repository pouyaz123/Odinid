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
 * @property-read array $CompDomain
 */
class Experiences extends \Base\FormModel {

	const OldestYearLimitation = 75;

	public function getPostName() {
		return "UserExperiences";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnExperienceID"
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
	public $txtFromDate;
	public $txtToDate;
	public $chkToPresent;
	public $txtDescription;
	#
	public $UserID;

	public function getCompDomain() {
		static $Domain = '';
		if (!$Domain && $this->txtCompanyURL) {
			$Domain = parse_url($this->txtCompanyURL, PHP_URL_HOST);
			$Domain = ltrim($Domain, 'www.');
		}
		return $Domain;
	}

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
				'SQL' => 'SELECT COUNT(*) FROM `_user_experiences` WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->UserID),
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
			array('txtCompanyTitle, txtJobTitle', 'required'
				, 'on' => 'Add, Edit'),
			array('txtCompanyURL', 'url',
				'on' => 'Add, Edit'),
			array_merge(array('txtCompanyURL', 'length',
				'on' => 'Add, Edit'), $vl->WebAddress),
			array_merge(array('txtCompanyTitle, txtJobTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
			#
			array('chkHealthInsurance, chkOvertimePay, chkRetirementAccount, chkToPresent', 'boolean'
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

	public function ValidateDate($attr) {
		if ($this->$attr &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->$attr) &&
				strtotime($this->$attr . ' +0000') > time())
			$this->addError($attr, \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel($attr), '{value}' => $this->$attr)));
	}

	protected function afterValidate() {
		#max
		if (!$this->hdnExperienceID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_user_experiences` WHERE `UID`=:uid"
							, array(':uid' => $this->UserID));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxResumeBigItemsPerCase)
				$this->addError('', \t2::site_site('You have reached the maximum'));
		}
		#dates
		if ($this->txtFromDate && $this->txtToDate &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->txtFromDate) &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->txtToDate) &&
				strtotime($this->txtFromDate . ' +0000') > strtotime($this->txtToDate . ' +0000')) {
			$this->addError('txtFromDate', \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel('txtFromDate'), '{value}' => $this->txtFromDate)));
			$this->addError('txtToDate', \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel('txtToDate'), '{value}' => $this->txtToDate)));
		}
		#domain uniqueness
		$Domain = $this->CompDomain;
		if ($this->txtCompanyTitle && T\DB::GetField("SELECT COUNT(*) FROM `_company_info` WHERE `Title`!=:ttl AND (
				`Domain` <=> :dom
				OR `Domain` LIKE CONCAT('%', :dom_likeescaped) ESCAPE '='
				OR :dom LIKE CONCAT('%', `Domain`) ESCAPE '='
			)", array(
					':ttl' => $this->txtCompanyTitle? : null,
					':dom' => $Domain? : null,
					':dom_likeescaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				)))
			$this->addError('txtCompanyURL', \t2::site_site('This domain has been claimed by another company.'));
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
			'txtCompanyTitle' => \t2::site_site('Company title'),
			'txtCompanyURL' => \t2::site_site('Web URL'),
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
		if (!$this->hdnExperienceID) {
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
		$Domain = $this->CompDomain;
		$Queries[] = array(
			!$this->hdnExperienceID ?
					"INSERT INTO `_user_experiences`("
					. " `CombinedID`, `UID`, `CompanyID`"
					. ", `JobTitle`, `Level`, `EmploymentType`, `SalaryType`, `SalaryAmount`"
					. ", `TBALayoff`, `HealthInsurance`, `OvertimePay`, `WorkCondition`"
					. ", `RetirementAccount`, `RAPercent`"
					. ", `GeoCountryISO2`, `GeoDivisionCode`, `GeoCityID`"
					. ", `UserCountryID`,`UserDivisionID`, `UserCityID`"
					. ", `FromDate`, `ToDate`, `ToPresent`, `Description`)"
					. " VALUE("
					. " @expr_ID:=($strSQLPart_ID), :uid, @expr_CompanyID:=companies_getCreatedCompanyID(:compid, :compttl, :compdom, :compdom_escaped, :compurl)"
					. ", :jobttl, :lvl, :emptype, :saltype, :salamount"
					. ", :tba, :insur, :ovrtpay, :wrkcnd"
					. ", :retaccount, :retpercent"
					. ", @expr_CountryISO2, @expr_DivisionCombined, @expr_CityID"
					. ", @expr_UserCountryID, @expr_UserDivisionID, @expr_UserCityID"
					. ", :frmdt, :todt, :toprsnt, :desc)" :
					"UPDATE `_user_experiences` SET "
					. " `CompanyID`=@expr_CompanyID:=companies_getCreatedCompanyID(:compid, :compttl, :compdom, :compdom_escaped, :compurl)"
					. ", `JobTitle`=:jobttl, `Level`=:lvl, `EmploymentType`=:emptype, `SalaryType`=:saltype, `SalaryAmount`=:salamount"
					. ", `TBALayoff`=:tba, `HealthInsurance`=:insur, `OvertimePay`=:ovrtpay, `WorkCondition`=:wrkcnd"
					. ", `RetirementAccount`=:retaccount, `RAPercent`=:retpercent"
					. ", `GeoCountryISO2`=@expr_CountryISO2, `GeoDivisionCode`=@expr_DivisionCombined, `GeoCityID`=@expr_CityID"
					. ", `UserCountryID`=@expr_UserCountryID, `UserDivisionID`=@expr_UserDivisionID, `UserCityID`=@expr_UserCityID"
					. ", `FromDate`=:frmdt, `ToDate`=:todt, `ToPresent`=:toprsnt, `Description`=:desc"
					. " WHERE `CombinedID`=(@expr_ID:=:combid) AND `UID`=:uid"
			, array(
				':combid' => $this->hdnExperienceID,
				':uid' => $this->UserID,
				#
				':compid' => $this->hdnCompanyID? : null,
				':compttl' => $this->txtCompanyTitle? : null,
				':compdom' => $Domain? : null,
				':compdom_escaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				':compurl' => $this->txtCompanyURL? : null,
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
			if (!$this->hdnExperienceID || !$this->hdnCompanyID) {
				$dr = T\DB::GetRow("SELECT @expr_ID AS CombinedID, @expr_CompanyID AS CompanyID");
				$this->hdnExperienceID = $dr['CombinedID'];
				$this->hdnCompanyID = $dr['CompanyID'];
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
					':combid' => $this->hdnExperienceID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtExperiences($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
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
							. ", ci.`Title` AS CompanyTitle"
							. ", ci.`URL` AS CompanyURL"
							. " FROM `_user_experiences` AS uexp"
							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " uexp.`CombinedID`=:id AND " : '') . " uexp.UID=:uid"
							. " INNER JOIN `_company_info` AS ci ON ci.`ID`=uexp.CompanyID"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=uexp.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=uexp.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =uexp.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=uexp.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=uexp.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=uexp.`UserCityID`"
							. ($DGP ?
									" WHERE {$DGP->SQLWhereClause}"
									. " ORDER BY {$DGP->Sort}"
									. " LIMIT $Limit" : "")
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
		static $F = true;
		$R = $this->getdtExperiences($ID, $F);
		$F = false;
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
				'ddlCountry' => $dr['GeoCountryISO2'] ? : ($dr['Country'] ? '_other_' : ''),
				'ddlDivision' => $dr['GeoDivisionCode'] ? : ($dr['Division'] ? '_other_' : ''),
				'ddlCity' => $dr['GeoCityID'] ? : ($dr['City'] ? '_other_' : ''),
				'txtCountry' => $dr['GeoCountryISO2'] ? : $dr['Country'],
				'txtDivision' => $dr['GeoDivisionCode'] ? : $dr['Division'],
				'txtCity' => $dr['GeoCityID'] ? : $dr['City'],
				#
				'txtFromDate' => $dr['FromDate'],
				'txtToDate' => $dr['ToDate'],
				'chkToPresent' => $dr['ToPresent'],
				'txtDescription' => $dr['Description'],
			);
			$this->attributes = $arrAttrs;
		}
	}

	static function AC_Comp_GetSuggestions($term) {
		if ($term) {
			$dt = T\DB::GetTable("SELECT `Title`, `URL`, `ID`"
							. " FROM `_company_info`"
							. " WHERE `Title` LIKE CONCAT(" . T\DB::MySQLConvert(':term', 2) . ", '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
							, array(':term' => T\DB::EscapeLikeWildCards($term)));
			if ($dt) {
				foreach ($dt as $idx => $dr) {
					$item = array(
						'label' => "<div rel='" . json_encode(array('ID' => $dr['ID'], 'URL' => $dr['URL'])) . "'>{$dr['Title']}" . ($dr['URL'] ? " ({$dr['URL']})" : '') . "</div>"
						, 'value' => $dr['Title']);
					$dt[$idx] = $item;
				}
				return json_encode($dt);
			}
		}
	}

}
