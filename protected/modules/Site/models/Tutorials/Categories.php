<?php

namespace Site\models\Tutorials;

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
		return "TutCat";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnID";
	}

	//----- attrs
	public $hdnID;
	public $txtTitle;
	#
	public $UserID;

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('hdnID', 'required',
				'on' => 'Edit, Delete'),
			array('hdnID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_tutorial_cats` WHERE `ID`=:val AND `UID`=:uid',
				'SQLParams' => array(':uid' => $this->UserID),
				'on' => 'Edit, Delete'),
			#
			array('txtTitle', 'required'
				, 'on' => 'Add, Edit'),
			array('txtTitle', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_tutorial_cats` WHERE `UID`=:uid AND `Title`=:val',
				'SQLParams' => array(':uid' => $this->UserID),
				'on' => 'Add, Edit'),
			array_merge(array('txtTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
		);
	}

	protected function afterValidate() {
		if (!$this->hdnID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_tutorial_cats` WHERE `UID`=:uid"
							, array(':uid' => $this->UserID));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxTutCats)
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
		if (!$this->hdnID)
			$ID = T\DB::GetNewID_Combined(
							'_tutorial_cats'
							, 'ID'
							, 'UID=:uid'
							, array(':uid' => $this->UserID)
							, array(
						'ReturnTheQuery' => false,
						'PrefixQuery' => "CONCAT(:uid, '_')",
							)
			);
		$Result = T\DB::Execute(
						!$this->hdnID ?
								"INSERT INTO `_tutorial_cats`(`ID`, `UID`, `Title`) VALUES(:id, :uid, :ttl)" :
								"UPDATE `_tutorial_cats` SET `Title`=:ttl WHERE `ID`=:id AND `UID`=:uid"
						, array(
					':id' => $this->hdnID? : $ID,
					':ttl' => $this->txtTitle,
					':uid' => $this->UserID,
						)
		);
		if ($Result && !$this->hdnID && $this->scenario == "Add") {
			$this->scenario = 'Edit';
			$this->hdnID = $ID;
		}
		return $Result ? true : false;
	}

	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		$Result = T\DB::Execute("DELETE FROM `_tutorial_cats` WHERE `ID`=:id AND `UID`=:uid"
						, array(
					':id' => $this->hdnID,
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
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_tutorial_cats` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT *"
							. " FROM `_tutorial_cats`"
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
		$dr = $this->getdtCategories($this->hdnID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnID' => $dr['ID'],
				'txtTitle' => $dr['Title'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
