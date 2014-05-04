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
 * @property-read array $dtProjects
 * @property-read array $dtFreshProjects
 * @property-read array $arrStatuses
 * @property-read array $arrDividerLineTypes
 */
class Project extends \Base\FormModel {

	public function getPostName() {
		return "Prj";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnPrjID";
	}

	//----- attrs
	public $hdnPrjID;
	#
	public $txtTitle;
	public $txtSmallDesc;
	public $chkReel;
	public $ddlSatus;
	public $fileThumbnail;
	public $hdnThumbnailCrop;
	public $chkVisibility;
	public $hdnCats;
	public $chkAdult;
	public $txtPassword;
	public $ddlDividerLineType;
	public $txtContentSpacing;
	#
	public $txtWorkFields;
	public $txtTools;
	public $txtTags;
	public $txtSkills;
	#
	public $UserID;

	public function getarrStatuses() {
		return array(
			'Finished' => 'Finished',
			'WIP' => 'WIP',
		);
	}
	public function getarrDividerLineTypes() {
		return array(
			'none' => '',
			'solid' => '___',
			'dashline' => '---',
			'dotline' => '...',
		);
	}

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('$hdnPrjID', 'required',
				'on' => 'Edit, Delete'),
			array('$hdnPrjID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_projects` WHERE `ID`=:val AND `UID`=:uid',
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
		if (!$this->$hdnPrjID) {//means in add mode not edit mode
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
						!$this->hdnPrjID ?
								"INSERT INTO `_project_pcats`(`UID`, `Title`) VALUES(:uid, :ttl)" :
								"UPDATE `_project_pcats` SET `Title`=:ttl WHERE `ID`=:id AND `UID`=:uid"
						, array(
					':id' => $this->hdnPrjID,
					':ttl' => $this->txtTitle,
					':uid' => $this->UserID,
						)
		);
		if ($Result && !$this->hdnPrjID && $this->scenario == "Add") {
			$this->scenario = 'Edit';
			$this->hdnPrjID = T\DB::GetField("SELECT LAST_INSERT_ID()");
		}
		return $Result ? true : false;
	}

	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		$Result = T\DB::Execute("DELETE FROM `_project_pcats` WHERE `ID`=:id AND `UID`=:uid"
						, array(
					':id' => $this->$hdnPrjID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtProjects($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
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
	public function getdtFreshProjects($ID = null) {
		static $F = true;
		$R = $this->getdtProjects($ID, $F);
		$F = false;
		return $R;
	}

	public function SetForm() {
		$dr = $this->getdtProjects($this->$hdnPrjID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'$hdnPrjID' => $dr['ID'],
				'txtTitle' => $dr['Title'],
			);
			$this->attributes = $arrAttrs;
		}
	}

}
