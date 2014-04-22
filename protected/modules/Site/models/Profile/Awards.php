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
 * @property-read array $arrYears
 */
class Awards extends \Base\FormModel {

	const OldestYearLimitation = 50;

	public function getPostName() {
		return "UserAwards";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnAwardID"
				. ", ddlYear"
				. ", hdnOrganizationID";
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

	public function getarrYears() {
		$CurrentYear = gmdate('Y');
		$Result = array();
		for ($x = 0; $x < self::OldestYearLimitation; $x++) {
			$Result[$CurrentYear - $x] = ($CurrentYear - $x);
			ksort($Result);
		}
		return $Result;
	}

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
			'ddlYear' => \t2::site_site('Year'),
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
					"INSERT IGNORE INTO `_user_awards`("
					. " `CombinedID`, `UID`, `OrganizationID`"
					. ", `Title`, `Year`, `Description`)"
					. " VALUE("
					. " @awrd_ID:=($strSQLPart_ID), :uid, @awrd_OrganizationID:=organizations_getCreatedOrganizationID(:orgid, :orgttl, :orgdom, :orgdom_escaped, :orgulr)"
					. ", :ttl, :year, :desc)" :
					"UPDATE `_user_awards` SET "
					. " `OrganizationID`=@awrd_OrganizationID:=organizations_getCreatedOrganizationID(:orgid, :orgttl, :orgdom, :orgdom_escaped, :orgulr)"
					. ", `Title`=:ttl, `Year`=:year, `Description`=:desc"
					. " WHERE `CombinedID`=(@awrd_ID:=:combid) AND `UID`=:uid"
			, array(
				':combid' => $this->hdnAwardID,
				':uid' => $this->UserID,
				#
				':orgid' => $this->hdnOrganizationID? : null,
				':orgttl' => $this->txtOrganizationTitle? : null,
				':orgdom' => $Domain? : null,
				':orgdom_escaped' => $Domain ? T\DB::EscapeLikeWildCards($Domain) : null,
				':orgulr' => $this->txtOrganizationURL? : null,
				#
				':ttl' => $this->txtTitle? : null,
				':year' => $this->ddlYear? : null,
				':desc' => $this->txtDescription? : null,
			)
		);
		$Result = T\DB::Transaction($Queries, NULL, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::site_site('Failed! Plz retry.'));
				});
		if ($Result) {
			$this->scenario = 'Edit';
			if (!$this->hdnAwardID || !$this->hdnOrganizationID) {
				$dr = T\DB::GetRow("SELECT @awrd_ID AS CombinedID, @awrd_OrganizationID AS OrganizationID");
				$this->hdnAwardID = $dr['CombinedID'];
				$this->hdnOrganizationID = $dr['OrganizationID'];
			}
		}
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

	public function getdtAwards($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			if ($DGP) {
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_awards` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT uawrd.*"
							. ", org.`Title` AS OrganizationTitle"
							. ", org.`URL` AS OrganizationURL"
							. " FROM `_user_awards` AS uawrd"
							. " INNER JOIN (SELECT 1) tmp ON " . ($ID ? " uawrd.`CombinedID`=:id AND " : "") . " UID=:uid"
							. " LEFT JOIN `_organizations` AS org ON org.`ID`=uawrd.OrganizationID"
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
				'ddlYear' => $dr['Year'],
				'txtDescription' => $dr['Description'],
				#
				'txtOrganizationTitle' => $dr['OrganizationTitle'],
				'txtOrganizationURL' => $dr['OrganizationURL'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
