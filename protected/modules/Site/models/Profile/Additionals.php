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
 * @property-read array $dtAdditionals
 * @property-read array $dtFreshAdditionals
 * @property-read array $arrYears
 */
class Additionals extends \Base\FormModel {

	public function getPostName() {
		return "UserAdditionals";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnAdditionalID";
	}

	//----- attrs
	public $hdnAdditionalID;
	public $txtTitle;
	public $txtDescription;
	#
	public $UserID;

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnAdditionalID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnAdditionalID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_user_additionals` WHERE `CombinedID`=:val',
				'on' => 'Edit, Delete'),
			#
			array('txtTitle', 'required'
				, 'on' => 'Add, Edit'),
			array_merge(array('txtTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
			array_merge(array('txtDescription', 'length',
				'on' => 'Add, Edit'), $vl->Description),
		);
	}

	public function attributeLabels() {
		return array(
			'txtTitle' => \t2::site_site('Title'),
			'txtDescription' => \t2::site_site('Description'),
		);
	}

	public function Save() {
		if (!$this->validate())
			return false;
		$Queries = array();
		if (!$this->hdnAdditionalID) {
			$strSQLPart_ID = T\DB::GetNewID_Combined(
							'_user_additionals'
							, 'CombinedID'
							, 'UID=:uid'
							, array($this->UserID)
							, array('PrefixQuery' => "CONCAT(:uid, '_')"));
		}
		$Queries[] = array(
			!$this->hdnAdditionalID ?
					"INSERT IGNORE INTO `_user_additionals`("
					. " `CombinedID`, `UID`"
					. ", `Title`, `Description`)"
					. " VALUE("
					. " @adds_ID:=($strSQLPart_ID), :uid"
					. ", :ttl, :desc)" :
					"UPDATE `_user_additionals` SET "
					. "`Title`=:ttl, `Description`=:desc"
					. " WHERE `CombinedID`=(@adds_ID:=:combid) AND `UID`=:uid"
			, array(
				':combid' => $this->hdnAdditionalID,
				':uid' => $this->UserID,
				#
				':ttl' => $this->txtTitle? : null,
				':desc' => $this->txtDescription? : null,
			)
		);
		$Result = T\DB::Transaction($Queries, NULL, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::site_site('Failed! Plz retry.'));
				});
		if ($Result) {
			$this->scenario = 'Edit';
			if (!$this->hdnAdditionalID) {
				$dr = T\DB::GetRow("SELECT @adds_ID AS CombinedID");
				$this->hdnAdditionalID = $dr['CombinedID'];
			}
		}
		return $Result ? true : false;
	}

	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		$Result = T\DB::Execute("DELETE FROM `_user_additionals` WHERE `CombinedID`=:combid AND `UID`=:uid"
						, array(
					':combid' => $this->hdnAdditionalID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtAdditionals($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			if ($DGP) {
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_additionals` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT *"
							. " FROM `_user_additionals`"
							. " WHERE " . ($ID ? " `CombinedID`=:id AND " : "") . " UID=:uid"
							. ($DGP ?
									" AND {$DGP->SQLWhereClause}"
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
	public function getdtFreshAdditionals($ID = null) {
		static $R = null;
		if (!$R)
			$R = $this->getdtAdditionals($ID, true);
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtAdditionals($this->hdnAdditionalID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnAdditionalID' => $dr['CombinedID'],
				'txtTitle' => $dr['Title'],
				'txtDescription' => $dr['Description'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
