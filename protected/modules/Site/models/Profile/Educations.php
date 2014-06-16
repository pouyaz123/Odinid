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
 * @property-read array $dtEducations
 * @property-read array $dtFreshEducations
 * @property-read array $SchoolDomain
 */
class Educations extends \Base\FormModel {

	const OldestYearLimitation = 75;

	public function getPostName() {
		return "UserEducations";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnEducationID"
				. ", UserID"
				. ", hdnSchoolID"
				. ", chkToPresent"
				. ", txtFromDate"
				. ", txtToDate";
	}

	//----- attrs
	public $hdnEducationID;
	#
	public $hdnSchoolID;
	public $txtSchoolTitle;
	public $txtSchoolURL;
	public $txtStudyField;
	public $txtDegree;
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

	public function getSchoolDomain() {
		static $Domain = '';
		if (!$Domain && $this->txtSchoolURL) {
			$Domain = parse_url($this->txtSchoolURL, PHP_URL_HOST);
			$Domain = ltrim($Domain, 'www.');
		}
		return $Domain;
	}

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnEducationID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnEducationID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_educations` WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->UserID),
				'on' => 'Edit, Delete'),
			#
			array('txtFromDate, txtToDate', 'date',
				'format' => C\Regexp::Yii_DateFormat_FullDigit,
				'on' => 'Add, Edit'),
			array('txtFromDate, txtToDate', 'ValidateDate',
				'on' => 'Add, Edit'),
			array('chkToPresent', 'boolean'
				, 'on' => 'Add, Edit'),
			#
			array('txtSchoolTitle, txtStudyField, txtDegree', 'required'
				, 'on' => 'Add, Edit'),
			array_merge(array('txtSchoolTitle, txtDegree, txtStudyField', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
			array('txtSchoolURL', 'url',
				'on' => 'Add, Edit'),
			array_merge(array('txtSchoolURL', 'length',
				'on' => 'Add, Edit'), $vl->WebAddress),
			array_merge(array('txtDescription', 'length',
				'on' => 'Add, Edit'), $vl->Description),
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
		if (!$this->hdnEducationID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_user_educations` WHERE `UID`=:uid"
							, array(':uid' => $this->UserID));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxResumeBigItemsPerCase)
				$this->addError('', \t2::site_site('You have reached the maximum'));
		}
		#dates
		if ($this->txtFromDate && $this->txtToDate &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->txtFromDate) &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->txtToDate) &&
				strtotime($this->txtFromDate . ' +0000') > strtotime($this->txtToDate . ' +0000')
		) {
			$this->addError('txtFromDate', \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel('txtFromDate'), '{value}' => $this->txtFromDate)));
			$this->addError('txtToDate', \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel('txtToDate'), '{value}' => $this->txtToDate)));
		}
		#domain uniqueness
		$Domain = $this->SchoolDomain;
		if ($this->txtSchoolTitle && T\DB::GetField("SELECT COUNT(*) FROM `_school_info` WHERE `Title`!=:ttl AND (
				`Domain` <=> :dom
				OR `Domain` LIKE CONCAT('%', :dom_likeescaped) ESCAPE '='
				OR :dom LIKE CONCAT('%', `Domain`) ESCAPE '='
			)", array(
					':ttl' => $this->txtSchoolTitle? : null,
					':dom' => $Domain? : null,
					':dom_likeescaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				))) {
			$this->addError('txtSchoolURL', \t2::site_site('This domain has been occupied for another school.'));
		}
		#edu uniqueness
		if ($this->scenario == 'Add' || $this->scenario == 'Edit') {
			if (T\DB::GetField("SELECT COUNT(*)"
							. " FROM `_user_educations` uedu"
							. " INNER JOIN (SELECT 1) tmp ON uedu.`UID`=:uid " . ($this->hdnEducationID ? " AND uedu.`CombinedID`!=:id" : "")
							. " INNER JOIN `_education_studyfields` edustdy ON edustdy.`StudyField`=:stdyfld AND edustdy.`ID`=uedu.`StudyFieldID`"
							. " INNER JOIN `_education_degrees` edudgre ON edudgre.`Degree`=:dgrefld AND edudgre.`ID`=uedu.`DegreeID`"
							. " INNER JOIN `_school_info` scl ON uedu.`SchoolID`=scl.`ID`"
							. " AND (" . ($this->hdnSchoolID ? " uedu.`SchoolID`=:sclid OR " : "")
							. " (scl.`Title`=:sclttl AND"
							. "	 ("
							. "		scl.`Domain` <=> :scldom"
							. "		OR scl.`Domain` LIKE CONCAT('%', :sclEscDom) ESCAPE '='"
							. "		OR :scldom LIKE CONCAT('%', scl.`Domain`) ESCAPE '='"
							. "  )"
							. " )"
							. ")"
							, array(
						':id' => $this->hdnEducationID,
						':uid' => $this->UserID,
						':stdyfld' => $this->txtStudyField,
						':dgrefld' => $this->txtDegree,
						':sclid' => $this->hdnSchoolID? : null,
						':sclttl' => $this->txtSchoolTitle,
						':scldom' => $this->SchoolDomain? : null,
						':sclEscDom' => T\DB::EscapeLikeWildCards($this->SchoolDomain)? : null,
							)
					)
			) {
				$this->addError('txtSchoolTitle', \t2::yii('This combination has been taken previously'));
				$this->addError('txtStudyField', \t2::yii('This combination has been taken previously'));
				$this->addError('txtDegree', \t2::yii('This combination has been taken previously'));
			}
		}
	}

	public function attributeLabels() {
		return array(
			'txtSchoolTitle' => \t2::site_site('School title'),
			'txtSchoolURL' => \t2::site_site('Web URL'),
			'txtStudyField' => \t2::site_site('Study field'),
			'txtDegree' => \t2::site_site('Degree'),
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
							'_user_educations'
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
				, @edu_CountryISO2
				, @edu_DivisionCombined
				, @edu_DivisionCode
				, @edu_CityID);
			CALL geo_getUserLocationIDs(
				:country
				, :division
				, :city
				, @edu_CountryISO2
				, @edu_DivisionCombined
				, @edu_CityID
				, @edu_UserCountryID
				, @edu_UserDivisionID
				, @edu_UserCityID)"
			, array(
				':country' => ($this->txtCountry? : $this->ddlCountry)? : NULL,
				':division' => ($this->txtDivision? : $this->ddlDivision)? : NULL,
				':city' => ($this->txtCity? : $this->ddlCity)? : NULL,
			)
		);
		$Domain = $this->SchoolDomain;
		$Queries[] = array(
			!$this->hdnEducationID ?
					"INSERT INTO `_user_educations`("
					. " `CombinedID`, `UID`, `SchoolID`"
					. ", `StudyFieldID`, `DegreeID`"
					. ", `GeoCountryISO2`, `GeoDivisionCode`, `GeoCityID`"
					. ", `UserCountryID`,`UserDivisionID`, `UserCityID`"
					. ", `FromDate`, `ToDate`, `ToPresent`, `Description`)"
					. " VALUE("
					. " @edu_ID:=($strSQLPart_ID), :uid, @edu_SchoolID:=schools_getCreatedSchoolID(:schlid, :schlttl, :schldom, :schldom_escaped, :schlurl)"
					. ", edu_getCreatedStudyFieldID(:stdyfld), edu_getCreatedDegreeID(:dgr)"
					. ", @edu_CountryISO2, @edu_DivisionCombined, @edu_CityID"
					. ", @edu_UserCountryID, @edu_UserDivisionID, @edu_UserCityID"
					. ", :frmdt, :todt, :toprsnt, :desc)" :
					"UPDATE `_user_educations` SET "
					. " `SchoolID`=@edu_SchoolID:=schools_getCreatedSchoolID(:schlid, :schlttl, :schldom, :schldom_escaped, :schlurl)"
					. ", `StudyFieldID`=edu_getCreatedStudyFieldID(:stdyfld)"
					. ", `DegreeID`=edu_getCreatedDegreeID(:dgr)"
					. ", `GeoCountryISO2`=@edu_CountryISO2, `GeoDivisionCode`=@edu_DivisionCombined, `GeoCityID`=@edu_CityID"
					. ", `UserCountryID`=@edu_UserCountryID, `UserDivisionID`=@edu_UserDivisionID, `UserCityID`=@edu_UserCityID"
					. ", `FromDate`=:frmdt, `ToDate`=:todt, `ToPresent`=:toprsnt, `Description`=:desc"
					. " WHERE `CombinedID`=(@edu_ID:=:combid) AND `UID`=:uid"
			, array(
				':combid' => $this->hdnEducationID,
				':uid' => $this->UserID,
				#
				':schlid' => $this->hdnSchoolID? : null,
				':schlttl' => $this->txtSchoolTitle? : null,
				':schldom' => $Domain? : null,
				':schldom_escaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				':schlurl' => $this->txtSchoolURL? : null,
				#
				':stdyfld' => $this->txtStudyField? : null,
				':dgr' => $this->txtDegree? : null,
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
				$dr = T\DB::GetRow("SELECT @edu_ID AS CombinedID, @edu_SchoolID AS SchoolID");
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
		$Result = T\DB::Execute("DELETE FROM `_user_educations` WHERE `CombinedID`=:combid AND `UID`=:uid"
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
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_educations` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT uedu.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", IFNULL(gct.`AsciiName`, guct.`City`) AS City"
							. ", si.`Title` AS SchoolTitle"
							. ", si.`URL` AS SchoolURL"
							. ", std.`StudyField`"
							. ", dgr.`Degree`"
							. " FROM `_user_educations` AS uedu"
							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " uedu.`CombinedID`=:id AND " : '') . " uedu.UID=:uid"
							. " INNER JOIN `_school_info` AS si ON si.`ID`=uedu.SchoolID"
							. " INNER JOIN `_education_studyfields` AS std ON std.`ID`=uedu.`StudyFieldID`"
							. " INNER JOIN `_education_degrees` AS dgr ON dgr.`ID`=uedu.DegreeID"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=uedu.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=uedu.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =uedu.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=uedu.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=uedu.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=uedu.`UserCityID`"
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
	public function getdtFreshEducations($ID = null) {
		static $F = true;
		$R = $this->getdtEducations($ID, $F);
		$F = false;
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtEducations($this->hdnEducationID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnEducationID' => $dr['CombinedID'],
				'hdnSchoolID' => $dr['SchoolID'],
				'txtStudyField' => $dr['StudyField'],
				'txtDegree' => $dr['Degree'],
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
				#
				'txtFromDate' => $dr['FromDate'],
				'txtToDate' => $dr['ToDate'],
				'chkToPresent' => $dr['ToPresent'],
				'txtDescription' => $dr['Description'],
			);
			$this->attributes = $arrAttrs;
		}
	}

	static function AC_School_GetSuggestions($term) {
		if ($term) {
			$dt = T\DB::GetTable("SELECT `Title`, `URL`, `ID`"
							. " FROM `_school_info`"
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

	static function AC_StudyField_GetSuggestions($term) {
		if ($term) {
			$dt = T\DB::GetTable("SELECT `StudyField`"
							. " FROM `_education_studyfields`"
							. " WHERE `StudyField` LIKE CONCAT(:term, '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
							, array(':term' => T\DB::EscapeLikeWildCards($term)));
			if ($dt) {
				foreach ($dt as $idx => $dr)
					$dt[$idx] = $dr['StudyField'];
				return json_encode($dt);
			}
		}
	}

	static function AC_Degree_GetSuggestions($term) {
		if ($term) {
			$dt = T\DB::GetTable("SELECT `Degree`"
							. " FROM `_education_degrees`"
							. " WHERE `Degree` LIKE CONCAT(:term, '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
							, array(':term' => T\DB::EscapeLikeWildCards($term)));
			if ($dt) {
				foreach ($dt as $idx => $dr)
					$dt[$idx] = $dr['Degree'];
				return json_encode($dt);
			}
		}
	}

}
