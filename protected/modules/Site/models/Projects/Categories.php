<?php

namespace Site\models\Projects;

use \Consts as C;
use \Tools as T;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read array $dtCategories
 * @property-read array $dtFreshCategories
 */
class Categories extends \Base\FormModel {

	public function getPostName() {
		return "PrjCat";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnCatID";
	}

	//----- attrs
	public $hdnCatID;
	public $txtTitle;
	#
	public $UserID;

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnCatID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnCatID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_project_pcats` WHERE `ID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->UserID),
				'on' => 'Edit, Delete'),
			#
			array('txtTitle', 'required'
				, 'on' => 'Add, Edit'),
			array('txtTitle', 'Unique',
				'SQL' => 'SELECT COUNT(*) FROM `_project_pcats` WHERE `UID`=:uid AND `Title`=:val',
				'on' => 'Add, Edit'),
			array_merge(array('txtTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
		);
	}

	protected function afterValidate() {
		if (!$this->hdnCatID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_project_pcats` WHERE `UID`=:uid"
							, array(':uid' => $this->UserID));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxProjectCats)
				$this->addError('', \t2::site_site('You have reached the maximum'));
		}
	}

	public function attributeLabels() {
		return array(
			'txtTitle' => \t2::site_site('Title'),
		);
	}

	public function Save() {
		if (!$this->validate())
			return false;
		$Result = T\DB::Execute(
						!$this->hdnCatID ?
								"INSERT INTO `_project_pcats`(`UID`, `Title`) VALUES(:uid, :ttl)" :
								"UPDATE `_project_pcats` SET `Title`=:ttl WHERE `ID`=:id AND `UID`=:uid"
						, array(
					':id' => $this->hdnCatID,
					':ttl' => $this->txtTitle,
					':uid' => $this->UserID,
						)
		);
		if ($Result && !$this->hdnCatID && $this->scenario == "Add") {
			$this->scenario = 'Edit';
			$this->hdnCatID = T\DB::GetField("SELECT LAST_INSERT_ID()");
		}
		return $Result ? true : false;
	}

	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		$Result = T\DB::Execute("DELETE FROM `_project_pcats` WHERE `ID`=:id AND `UID`=:uid"
						, array(
					':id' => $this->hdnCatID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtCategories($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			if ($DGP) {
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_project_pcats` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT *"
							. " FROM `_project_pcats`"
							. " WHERE " . ($ID ? " `ID`=:id AND " : '') . " UID=:uid"
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
	public function getdtFreshCategories($ID = null) {
		static $F = true;
		$R = $this->getdtCategories($ID, $F);
		$F = false;
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtCategories($this->hdnCatID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnCatID' => $dr['ID'],
				'txtTitle' => $dr['Title'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
