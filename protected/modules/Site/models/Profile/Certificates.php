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
 * @property-read array $InstDomain
 */
class Certificates extends \Base\FormModel {

	const OldestYearLimitation = 50;

	public function getPostName() {
		return "UserCertificates";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnCertificateID"
				. ", txtDate"
				. ", hdnInstitutionID";
	}

	//----- attrs
	public $hdnCertificateID;
	public $txtTitle;
	public $txtDate;
	public $txtDescription;
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

	public function getInstDomain() {
		static $Domain = '';
		if (!$Domain && $this->txtInstitutionURL) {
			$Domain = parse_url($this->txtInstitutionURL, PHP_URL_HOST);
			$Domain = ltrim($Domain, 'www.');
		}
		return $Domain;
	}

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnCertificateID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnCertificateID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_certificates` WHERE `CombinedID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->UserID),
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
			array('txtDate', 'date',
				'format' => C\Regexp::Yii_DateFormat_FullDigit,
				'on' => 'Add, Edit'),
			array('txtDate', 'ValidateDate',
				'on' => 'Add, Edit'),
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

	protected function afterValidate() {
		#max
		if (!$this->hdnCertificateID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_user_certificates` WHERE `UID`=:uid"
							, array(':uid' => $this->UserID));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxResumeBigItemsPerCase)
				$this->addError('', \t2::site_site('You have reached the maximum'));
		}
		#domain uniqueness
		$Domain = $this->InstDomain;
		if ($this->txtInstitutionTitle && T\DB::GetField("SELECT COUNT(*) FROM `_institutions` WHERE `Title`!=:ttl AND (
				`Domain` <=> :dom
				OR `Domain` LIKE CONCAT('%', :dom_likeescaped) ESCAPE '='
				OR :dom LIKE CONCAT('%', `Domain`) ESCAPE '='
			)", array(
					':ttl' => $this->txtInstitutionTitle? : null,
					':dom' => $Domain? : null,
					':dom_likeescaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				))) {
			$this->addError('txtInstitutionURL', \t2::site_site('This domain has been occupied for another institution.'));
		}
		#cert uniqueness
		if ($this->scenario == 'Add' || $this->scenario == 'Edit') {
			if (T\DB::GetField("SELECT COUNT(*)"
							. " FROM `_user_certificates` ucrt"
							. " INNER JOIN (SELECT 1) tmp ON ucrt.`UID`=:uid AND ucrt.`Title`=:ttl"
							. ($this->hdnCertificateID ? " AND ucrt.`CombinedID`!=:id" : "")
							. " INNER JOIN `_institutions` insts ON ucrt.`InstitutionID`=insts.`ID`"
							. " AND (" . ($this->hdnInstitutionID ? " ucrt.`InstitutionID`=:insid OR " : "")
							. " (insts.`Title`=:insttl AND"
							. "  ("
							. "		insts.`Domain` <=> :insdom"
							. "		OR insts.`Domain` LIKE CONCAT('%', :insEscDom) ESCAPE '='"
							. "		OR :insdom LIKE CONCAT('%', insts.`Domain`) ESCAPE '='"
							. "  )"
							. " )"
							. ")"
							, array(
						':id' => $this->hdnCertificateID,
						':uid' => $this->UserID,
						':ttl' => $this->txtTitle,
						':insid' => $this->hdnInstitutionID? : null,
						':insttl' => $this->txtInstitutionTitle,
						':insdom' => $this->InstDomain? : null,
						':insEscDom' => T\DB::EscapeLikeWildCards($this->InstDomain)? : null,
							)
					)
			) {
				$this->addError('txtInstitutionTitle', \t2::yii('This combination has been taken previously'));
				$this->addError('txtTitle', \t2::yii('This combination has been taken previously'));
			}
		}
	}

	public function ValidateDate($attr) {
		if ($this->$attr &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->$attr) &&
				strtotime($this->$attr . ' +0000') > time())
			$this->addError($attr, \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->getAttributeLabel($attr), '{value}' => $this->$attr)));
	}

	public function attributeLabels() {
		return array(
			'txtTitle' => \t2::site_site('Title'),
			'txtDescription' => \t2::site_site('Description'),
			'txtDate' => \t2::site_site('Date'),
			'txtInstitutionTitle' => \t2::site_site('Institution title'),
			'txtInstitutionURL' => \t2::site_site('Web URL'),
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
				, @cert_CountryISO2
				, @cert_DivisionCombined
				, @cert_DivisionCode
				, @cert_CityID);
			CALL geo_getUserLocationIDs(
				:country
				, :division
				, :city
				, @cert_CountryISO2
				, @cert_DivisionCombined
				, @cert_CityID
				, @cert_UserCountryID
				, @cert_UserDivisionID
				, @cert_UserCityID)"
			, array(
				':country' => ($this->txtCountry? : $this->ddlCountry)? : NULL,
				':division' => ($this->txtDivision? : $this->ddlDivision)? : NULL,
				':city' => ($this->txtCity? : $this->ddlCity)? : NULL,
			)
		);
		$Domain = $this->InstDomain;
		$Queries[] = array(
			!$this->hdnCertificateID ?
					"INSERT IGNORE INTO `_user_certificates`("
					. " `CombinedID`, `UID`, `InstitutionID`"
					. ", `Title`, `Date`, `Description`"
					. ", `GeoCountryISO2`, `GeoDivisionCode`, `GeoCityID`"
					. ", `UserCountryID`,`UserDivisionID`, `UserCityID`)"
					. " VALUE("
					. " @cert_ID:=($strSQLPart_ID), :uid, @cert_InstitutionID:=institutions_getCreatedInstitutionID(:insid, :insttl, :insdom, :insdom_escaped, :insulr)"
					. ", :ttl, :date, :desc"
					. ", @cert_CountryISO2, @cert_DivisionCombined, @cert_CityID"
					. ", @cert_UserCountryID, @cert_UserDivisionID, @cert_UserCityID)" :
					"UPDATE IGNORE `_user_certificates` SET "
					. " `InstitutionID`=@cert_InstitutionID:=institutions_getCreatedInstitutionID(:insid, :insttl, :insdom, :insdom_escaped, :insulr)"
					. ", `Title`=:ttl, `Date`=:date, `Description`=:desc"
					. ", `GeoCountryISO2`=@cert_CountryISO2, `GeoDivisionCode`=@cert_DivisionCombined, `GeoCityID`=@cert_CityID"
					. ", `UserCountryID`=@cert_UserCountryID, `UserDivisionID`=@cert_UserDivisionID, `UserCityID`=@cert_UserCityID"
					. " WHERE `CombinedID`=(@cert_ID:=:combid) AND `UID`=:uid"
			, array(
				':combid' => $this->hdnCertificateID,
				':uid' => $this->UserID,
				#
				':insid' => $this->hdnInstitutionID? : null,
				':insttl' => $this->txtInstitutionTitle? : null,
				':insdom' => $Domain? : null,
				':insdom_escaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				':insulr' => $this->txtInstitutionURL? : null,
				#
				':ttl' => $this->txtTitle? : null,
				':date' => $this->txtDate? : null,
				':desc' => $this->txtDescription? : null,
			)
		);
		$Result = T\DB::Transaction($Queries, NULL, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::site_site('Failed! Plz retry.'));
				});
		if ($Result) {
			$this->scenario = 'Edit';
			if (!$this->hdnCertificateID || !$this->hdnInstitutionID) {
				$dr = T\DB::GetRow("SELECT @cert_ID AS CombinedID, @cert_InstitutionID AS InstitutionID");
				$this->hdnCertificateID = $dr['CombinedID'];
				$this->hdnInstitutionID = $dr['InstitutionID'];
			}
		}
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

	public function getdtCertificates($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			if ($DGP) {
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_certificates` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT ucert.*"
							. ", IFNULL(gc.`AsciiName`, guc.`Country`) AS Country"
							. ", IFNULL(gd.`AsciiName`, gud.`Division`) AS Division"
							. ", IFNULL(gct.`AsciiName`, guct.`City`) AS City"
							. ", ins.`Title` AS InstitutionTitle"
							. ", ins.`URL` AS InstitutionURL"
							. " FROM `_user_certificates` AS ucert"
							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " ucert.`CombinedID`=:id AND " : '') . " ucert.UID=:uid"
							. " INNER JOIN `_institutions` AS ins ON ins.`ID`=ucert.InstitutionID"
							. " LEFT JOIN `_geo_countries` AS gc ON gc.`ISO2`=ucert.`GeoCountryISO2`"
							. " LEFT JOIN `_geo_divisions` AS gd ON gd.`CombinedCode`=ucert.`GeoDivisionCode`"
							. " LEFT JOIN `_geo_cities` AS gct ON gct.`GeonameID` =ucert.`GeoCityID`"
							. " LEFT JOIN `_geo_user_countries` AS guc ON guc.`ID`=ucert.`UserCountryID`"
							. " LEFT JOIN `_geo_user_divisions` AS gud ON gud.`ID`=ucert.`UserDivisionID`"
							. " LEFT JOIN `_geo_user_cities` AS guct ON guct.`ID`=ucert.`UserCityID`"
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
	public function getdtFreshCertificates($ID = null) {
		static $F = true;
		$R = $this->getdtCertificates($ID, $F);
		$F = false;
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtCertificates($this->hdnCertificateID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnCertificateID' => $dr['CombinedID'],
				'hdnInstitutionID' => $dr['InstitutionID'],
				'txtTitle' => $dr['Title'],
				'txtDate' => $dr['Date'],
				'txtDescription' => $dr['Description'],
				#
				'txtInstitutionTitle' => $dr['InstitutionTitle'],
				'txtInstitutionURL' => $dr['InstitutionURL'],
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

	static function AC_Insts_GetSuggestions($term) {
		if ($term) {
			$dt = T\DB::GetTable("SELECT `Title`, `URL`, `ID`"
							. " FROM `_institutions`"
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
