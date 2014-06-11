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
 * @property-read array $dtProjects
 * @property-read array $dtFreshProjects
 * @property-read array $arrStatuses
 * @property-read array $arrDividerLineTypes
 * @property-read string|integer $ThumbID
 * @property-read string|integer $FreshThumbID
 */
class Projects extends \Base\FormModel {

	public function getPostName() {
		return "Prj";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnID";
	}

	//----- attrs
	public $hdnID;
	#
	public $txtTitle = 'Untitled project';
	public $txtSmallDesc;
	public $chkIsReel = 0;
	public $chkPaidTutorial = 0;
	public $ddlStatus;
	public $fileThumb;
	public $hdnThumbCrop;
	public $chkVisibility = 1;
	public $hdnCatIDs;
	public $chkShowInHome = 1;
	public $chkAdult = 0;
	public $txtPassword;
	public $ddlDividerLineType;
	public $txtContentSpacing;
	#
	public $txtWorkFields;
	public $txtTools;
	public $txtTags;
	public $txtSkills;
	#
	public $hdnSchoolIDs;
	public $txtSchools;
	#
	public $hdnCompanyIDs;
	public $txtCompanies;
	#
	public $UserID;
	#tabular validation fields (only for validation)
	public $vldWorkField;
	public $vldTool;
	public $vldTag;
	public $vldSkill;
	public $vldSchool;
	public $vldCompany;

	const Type_Project = 'Project';
	const Type_Blog = 'Blog';
	const Type_Tutorial = 'Tutorial';

	public $Type = self::Type_Project;

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
		$rules = array(
			array('hdnID', 'required',
				'on' => 'Edit, Delete, Crop, Upload'),
			array('hdnID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_projects` WHERE `ID`=:val AND `UID`=:uid AND `Type`=:type',
				'SQLParams' => array(':uid' => $this->UserID, ':type' => $this->Type),
				'on' => 'Edit, Delete, Crop, Upload'),
			#
			array('txtTitle, ddlStatus', 'required'
				, 'on' => 'Add, Edit'),
			#
			array_merge(array('txtTitle', 'length'
				, 'on' => 'Add, Edit'), $vl->Title),
			array_merge(array('txtSmallDesc', 'length',
				'on' => 'Add, Edit'), $vl->SmallDesc),
			array_merge(array('txtContentSpacing', 'numerical'
				, 'on' => 'Add, Edit'), $vl->ContentSpacing),
			#
			array('chkVisibility, chkShowInHome, chkAdult', 'boolean',
				'on' => 'Add, Edit'),
			#
			array('ddlStatus', 'in',
				'range' => array_keys($this->arrStatuses),
				'on' => 'Add, Edit'),
			array('ddlDividerLineType', 'in',
				'range' => array_keys($this->arrDividerLineTypes),
				'on' => 'Add, Edit'),
			#
			array_merge(array('fileThumb', 'file',
				'on' => 'Add, Edit, Upload'), $vl->ProjectThumb),
			array('hdnThumbCrop', 'match',
				'pattern' => C\Regexp::CropDims,
				'on' => 'Add, Edit, Crop'),
			#
			array('hdnCatIDs, hdnSchoolIDs, hdnCompanyIDs', 'match',
				'pattern' => C\Regexp::HdnFieldIntIDs,
				'on' => 'Add, Edit'),
			array('txtWorkFields, txtTools, txtTags, txtSkills, txtSchools, txtCompanies', 'safe',
				'on' => 'Add, Edit')
		);
		switch ($this->Type) {
			case self::Type_Project:
				$rules = array_merge($rules, array(
					array('chkIsReel', 'boolean',
						'on' => 'Add, Edit'),
					array_merge(array('txtPassword', 'length'
						, 'on' => 'Add, Edit'), $vl->Password)
				));
				break;
//			case self::Type_Blog:
//				break;
			case self::Type_Tutorial:
				$rules[] = array('chkPaidTutorial', 'boolean',
					'on' => 'Add, Edit');
				break;
		}
		return $rules;
	}

	private $_ValidationPassed = false;

	protected function afterValidate() {
		if ($this->_ValidationPassed)
			return;
		$this->_ValidationPassed = true; //prevent recursion
		if ($this->scenario == 'Add') {
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_projects` WHERE `UID`=:uid"
							, array(':uid' => $this->UserID));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxProjects)
				$this->addError('', \t2::site_site('You have reached the maximum'));
		}
		//recursion's causer
		if ($this->scenario == 'Add' || $this->scenario == 'Edit') {
			$v = \ValidationLimits\User::GetInstance();
			$fncMassLenVld = function ($MassField, $vldField, $arrVldConf) {
				$Items = $this->$MassField;
				if ($Items) {
					$vld = new \CStringValidator();
					$vld->on = array('Add', 'Edit');
					$vld->attributes = array($vldField);
					T\Basics::ConfigureObject($vld, $arrVldConf);

					$Items = T\String::SafeExplode($Items);
					foreach ($Items as $Item) {
						$this->$vldField = $Item;
						$this->validate($vldField);
					}
				}
			};
			$fncMassLenVld('txtWorkFields', 'vldWorkField', $v->LongTitle);
			$fncMassLenVld('txtTools', 'vldTool', $v->LongTitle);
			$fncMassLenVld('txtTags', 'vldTag', $v->LongTitle);
			$fncMassLenVld('txtSkills', 'vldSkill', $v->LongTitle);
			$fncMassLenVld('txtSchools', 'vldSchool', $v->Title);
			$fncMassLenVld('txtCompanies', 'vldCompany', $v->Title);
		}
	}

	public function attributeLabels() {
		return array(
			'txtTitle' => \t2::site_site('Title'),
			'txtSmallDesc' => \t2::site_site('Small description'),
			'chkIsReel' => \t2::site_site('Is reel'),
			'chkPaidTutorial' => \t2::site_site('Is paid tutorial'),
			'ddlStatus' => \t2::site_site('Status'),
			'fileThumb' => \t2::site_site('Thumbnail'),
			'chkVisibility' => \t2::site_site('Visible'),
			'chkShowInHome' => \t2::site_site('Show in home page'),
			'chkAdult' => \t2::site_site('Adult'),
			'txtPassword' => \t2::site_site('Password'),
			'ddlDividerLineType' => \t2::site_site('Divider line type'),
			'txtContentSpacing' => \t2::site_site('Content spacing'),
			'txtWorkFields' => \t2::site_site('Work fields'),
			'txtTools' => \t2::site_site('Tools'),
			'txtTags' => \t2::site_site('Tags'),
			'txtSkills' => \t2::site_site('Skills'),
			'txtSchools' => \t2::site_site('Schools'),
			'txtCompanies' => \t2::site_site('Companies'),
		);
	}

	public function Save() {
		$Snrio = &$this->scenario;
		$Snrio = $this->hdnID ? 'Edit' : 'Add';
		if (!$this->validate())
			return false;
		$ID = $this->hdnID? : T\DB::GetNewID_Combined(
						'_projects'
						, 'ID'
						, 'UID=:uid'
						, array(':uid' => $this->UserID)
						, array(
					'ReturnTheQuery' => false,
					'PrefixQuery' => "CONCAT(:uid, '_')",
						)
		);
		$PicUnqID = $this->_UploadThumb();
		if (!$PicUnqID)
			$PicUnqID = $this->ThumbID;
		$Qries = array();
		$CommonParams = array(
			':id' => $ID,
			':uid' => $this->UserID,
		);
		$Qries[] = array(
			!$this->hdnID ?
					"INSERT INTO `_projects`("
					. "`ID`, `UID`, `Type`"
					. ($Snrio == 'Add' ?
							", `Title`, `SmallDesc`"
							. ", `IsReel`, `PaidTutorial`, `Status`"
							. ", `Visibility`, `ShowInHomePage`, `Adult`"
							. ", `Password`, `DividerLineType`, `ContentSpacing`"
							. ", `Thumbnail`" : "")
//					. ($Snrio == 'Upload' ? " `Thumbnail`" : "")
					. ") VALUES("
					. ($Snrio == 'Add' ? " :id, :uid, :type"
							. ", :ttl, :smldsc"
							. ", :reel, :paidtut, :status"
							. ", :vsblty, :home, :adult"
							. ", :pwd, :dvdr, :cntspc"
							. ", :thumb_unqid" : "")
//					. ($Snrio == 'Upload' ? " :thumb_unqid" : "")
					. ")" :
					"UPDATE `_projects` SET "
					. ($Snrio == 'Edit' ?
							" `Title`=:ttl, `SmallDesc`=:smldsc"
							. ", `IsReel`=:reel, `PaidTutorial`=:paidtut, `Status`=:status"
							. ", `Visibility`=:vsblty, `ShowInHomePage`=:home, `Adult`=:adult"
							. ", `Password`=:pwd, `DividerLineType`=:dvdr, `ContentSpacing`=:cntspc"
							. ", `Thumbnail`=:thumb_unqid, `ThumbnailCrop`=:thumbcrop" : "")//keep this additional comma
//					. ($Snrio == 'Upload' ? " `Thumbnail`=:thumb_unqid" : "")
//					. ($Snrio == 'Crop' ? " `ThumbnailCrop`=:thumbcrop" : "")
					. " WHERE `ID`=:id AND `UID`=:uid AND `Type`=:type"
			, array(
				':type' => $this->Type,
				':ttl' => $this->txtTitle,
				':smldsc' => $this->txtSmallDesc? : null,
				':reel' => $this->chkIsReel? : 0,
				':paidtut' => $this->chkPaidTutorial? : 0,
				':status' => $this->ddlStatus,
				':thumb_unqid' => $PicUnqID,
				':thumbcrop' => $this->hdnThumbCrop? : null,
				':vsblty' => $this->chkVisibility? : 0,
				':home' => $this->chkShowInHome? : 0,
				':adult' => $this->chkAdult? : 0,
				':pwd' => $this->txtPassword? : null,
				':dvdr' => $this->ddlDividerLineType? : null,
				':cntspc' => $this->txtContentSpacing? : null,
			)
		);
		if ($Snrio == 'Add' || $Snrio == 'Edit') {
			$this->GetSaveTransQuery_CatIDs($Qries, $ID);
			$this->GetSaveTransQuery_Tags($Qries, $ID, 'txtWorkFields'
					, '_project_workfields', 'WorkFieldID'
					, 'workFields_getCreatedWorkFieldID'
					, '_workfields', 'WorkField'
			);
			$this->GetSaveTransQuery_Tags($Qries, $ID, 'txtTools'
					, '_project_tools', 'ToolID'
					, 'tools_getCreatedToolID'
					, '_tools', 'Tool'
			);
			$this->GetSaveTransQuery_Tags($Qries, $ID, 'txtTags'
					, '_project_workfields', 'WorkFieldID'
					, 'simpletags_getCreatedSimpleTagID'
					, '_simpletags', 'Tag'
			);
			$this->GetSaveTransQuery_Tags($Qries, $ID, 'txtSkills'
					, '_project_skills', 'SkillID'
					, 'skills_getCreatedSkillID'
					, '_skills', 'Skill'
			);
			$this->GetSaveTransQuery_Tags($Qries, $ID, 'txtSchools'
					, '_project_schools', 'SchoolID'
					, 'schools_getCreatedSchoolID'
					, '_school_info', 'Title'
					, 'Profile', 'hdnSchoolIDs');
			$this->GetSaveTransQuery_Tags($Qries, $ID, 'txtCompanies'
					, '_project_companies', 'CompanyID'
					, 'companies_getCreatedCompanyID'
					, '_company_info', 'Title'
					, 'Profile', 'hdnCompanyIDs');
		}
		$Result = T\DB::Transaction($Qries, $CommonParams);
		if ($Result && !$this->hdnID && $Snrio == "Add") {
			$Snrio = 'Edit';
			$this->hdnID = $ID;
		}
		return $Result ? true : false;
	}

	/**
	 * @param array $Qries transaction queries
	 * @param type $ItemID
	 */
	private function GetSaveTransQuery_CatIDs(&$Qries, $ItemID) {
		$qryInsert = "INSERT IGNORE INTO `_project_cat_cnn`(`CatID`, `ItemID`)";
		$Prms = array(':itemid' => $ItemID);
		$CatIDPrms = array();
		if ($CatIDs = T\String::SafeExplode($this->hdnCatIDs)) {
			foreach ($CatIDs as $Idx => $CatID) {
				$qryInsert.=" VALUES(:catid$Idx, :itemid)";
				$CatIDPrms[":catid$Idx"] = $CatID;
			}
			if ($CatIDPrms)
				$Qries[] = array(
					$qryInsert
					, $Prms = array_merge($CatIDPrms, $Prms)
				);
		}
		if ($this->scenario == 'Edit' && $this->hdnID) {
			$Qries[] = array(
				"DELETE FROM `_project_cat_cnn` WHERE `ItemID`=:itemid"
				. ($CatIDPrms ? implode(" AND `CatID`!=", array_keys($CatIDPrms)) : "")
				, $Prms);
		}
	}

	/**
	 * @param array $Qries transaction queries
	 * @param type $ItemID
	 * @param type $Attr
	 * @param type $CnnDBTable
	 * @param type $CnnDBField_SrcID
	 * @param type $MySQLProcedure_Creator
	 * @param type $SrcDBTable
	 * @param type $SrcDBField_Tag
	 * @param string $Type can be Tag(for skills, tools, ...) or Profile(for company, school, ...)<br/>
	 * 	this type has been used to choose the right sort of parameters of the mysql procedure
	 * @return type
	 */
	private function GetSaveTransQuery_Tags(&$Qries, $ItemID, $Attr
	, $CnnDBTable, $CnnDBField_SrcID
	, $MySQLProcedure_Creator
	, $SrcDBTable, $SrcDBField_Tag
	, $Type = 'Tag', $TagIDsAttr = NULL) {
		$qryInsert = "INSERT IGNORE INTO `$CnnDBTable`(`ItemID`, `$CnnDBField_SrcID`)";
		$Prms = array(':itemid' => $ItemID);
		$TagPrms = array();
		if ($Type == 'Profile' && $TagIDsAttr)
			$TagIDs = T\String::SafeExplode($this->$TagIDsAttr);
		if ($Tags = T\String::SafeExplode($this->$Attr)) {
			foreach ($Tags as $Idx => $Tag) {
				$TagPrms[":tag$Idx"] = $Tag;
				if ($Type == 'Tag')
					$qryInsert.=" VALUES(:itemid, $MySQLProcedure_Creator(:tag$Idx))";
				elseif ($Type == 'Profile') {
					$TagPrms[":tagid$Idx"] = isset($TagIDs[$Idx]) && $TagIDs[$Idx] ? $TagIDs[$Idx] : NULL;
					$qryInsert.=" VALUES(:itemid, $MySQLProcedure_Creator(:tagid$Idx, :tag$Idx, NULL, NULL, NULL))";
				}
			}
			if ($TagPrms)
				$Qries[] = array(
					$qryInsert
					, $Prms = array_merge($TagPrms, $Prms)
				);
		}
		if ($this->scenario == 'Edit' && $this->hdnID) {
			$RemovableIDs = T\DB::GetField(
							"SELECT GROUP_CONCAT(src.`ID` SEPARATOR ',') AS IDs"
							. " FROM `$CnnDBTable` cnn"
							. " INNER JOIN (SELECT 1) tmp ON cnn.`ItemID` = :itemid"
							. " INNER JOIN `$SrcDBTable` src ON src.`ID` = cnn.`$CnnDBField_SrcID`"
							. ($TagPrms ?
									" WHERE src.`$SrcDBField_Tag` != " . implode(" AND src.`$SrcDBField_Tag` != ", array_keys($TagPrms)) :
									"")
							, $Prms);
			if (!$RemovableIDs)
				return;
			$RemovableIDs = explode(',', $RemovableIDs);
			$Qries[] = array(
				"DELETE FROM `$CnnDBTable` WHERE `ItemID`=:itemid AND (`$CnnDBField_SrcID` = " . implode(" OR `$CnnDBField_SrcID` = ", $RemovableIDs) . ")"
				, array(':itemid' => $ItemID)
			);
		}
	}

	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		if (!$this->_DeleteThumb())
			return false;
		$Result = T\DB::Execute("DELETE FROM `_projects` WHERE `ID`=:id AND `UID`=:uid"
						, array(
					':id' => $this->hdnID,
					':uid' => $this->UserID,
						)
		);
		if ($Result)
			$this->scenario = 'Add';
		return $Result;
	}

	public function getdtCategories() {
		return T\DB::GetTable("SELECT `ID`, `Title`"
						. " FROM `_project_cats`"
						. " WHERE `UID`=:uid AND `Type`=:type"
						. " ORDER BY `OrderNumber`"
						, array(':uid' => $this->UserID, ':type' => $this->Type));
	}

	public function getdtProjects($ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
		return $this->_getdtProjects('View', $ID, $refresh, $DGP);
	}

	private function _getdtProjects($Scenario = null, $ID = NULL, $refresh = false, \Base\DataGridParams $DGP = NULL) {
		$StaticIndex = $ID;
		if (!$StaticIndex)
			$StaticIndex = "ALL";
		static $arrDTs = array();
		if (!isset($arrDTs[$StaticIndex]) || $refresh) {
			if ($DGP) {
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_projects` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT prj.*"
							. ($Scenario == 'Edit' ?
									", GROUP_CONCAT(DISTINCT pcnn.ID SEPARATOR ',') AS CatIDs"
									. ", GROUP_CONCAT(DISTINCT wf.WorkField ORDER BY wf.WorkField ASC SEPARATOR ',') AS WorkFields"
									. ", GROUP_CONCAT(DISTINCT tl.Tool ORDER BY tl.Tool ASC SEPARATOR ',') AS Tools"
									. ", GROUP_CONCAT(DISTINCT tg.Tag ORDER BY tg.Tag ASC SEPARATOR ',') AS Tags"
									. ", GROUP_CONCAT(DISTINCT skl.Skill ORDER BY skl.Skill ASC SEPARATOR ',') AS Skills"
									. ", GROUP_CONCAT(DISTINCT comp.Title ORDER BY comp.Title ASC SEPARATOR ',') AS Companies"
									. ", GROUP_CONCAT(DISTINCT comp.Title SEPARATOR ',') AS CompanyIDs"
									. ", GROUP_CONCAT(DISTINCT scl.Title ORDER BY scl.Title ASC SEPARATOR ',') AS Schools"
									. ", GROUP_CONCAT(DISTINCT scl.Title SEPARATOR ',') AS SchoolIDs" : "")
							. " FROM `_projects` AS prj"
							. " INNER JOIN (SELECT 1) AS tmp ON " . ($ID ? " prj.`ID`=:id AND " : '') . " prj.UID=:uid"
							. ($Scenario == 'Edit' ?
									" LEFT JOIN `_project_cat_cnn` AS pcnn ON pcnn.ItemID=prj.ID"
									#
									. " LEFT JOIN `_project_workfields` AS wfcnn ON wfcnn.ItemID=prj.ID"
									. " LEFT JOIN `_workfields` AS wf ON wf.ID=wfcnn.WorkFieldID"
									. " LEFT JOIN `_project_tools` AS tlcnn ON tlcnn.ItemID=prj.ID"
									. " LEFT JOIN `_tools` AS tl ON tl.ID=tlcnn.ToolID"
									. " LEFT JOIN `_project_tags` AS tgcnn ON tgcnn.ItemID=prj.ID"
									. " LEFT JOIN `_tags` AS tg ON tg.ID=tgcnn.TagID"
									. " LEFT JOIN `_project_skills` AS sklcnn ON sklcnn.ItemID=prj.ID"
									. " LEFT JOIN `_skills` AS skl ON skl.ID=sklcnn.SkillID"
									#
									. " LEFT JOIN `_project_skills` AS sklcnn ON sklcnn.ItemID=prj.ID"
									. " LEFT JOIN `_skills` AS skl ON skl.ID=sklcnn.SkillID"
									. " LEFT JOIN `_project_skills` AS sklcnn ON sklcnn.ItemID=prj.ID"
									. " LEFT JOIN `_skills` AS skl ON skl.ID=sklcnn.SkillID"
									#
									. " LEFT JOIN `_project_companies` AS compcnn ON compcnn.ItemID=prj.ID"
									. " LEFT JOIN `_company_info` AS comp ON comp.ID=compcnn.CompanyID"
									. " LEFT JOIN `_project_schools` AS sclcnn ON sclcnn.ItemID=prj.ID"
									. " LEFT JOIN `_school_info` AS scl ON scl.ID=scl.SchoolID" : "")
							. ($DGP ? " WHERE AND {$DGP->SQLWhereClause}" : "")
							. " GROUP BY prj.ID"
							. ($DGP ?
									" ORDER BY {$DGP->Sort}"
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
		$dr = $this->getdtProjects($this->hdnID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnID' => $dr['ID'],
				'txtTitle' => $dr['Title'],
				'txtSmallDesc' => $dr['SmallDesc'],
				'chkIsReel' => $dr['IsReel'],
				'chkPaidTutorial' => $dr['PaidTutorial'],
				'ddlStatus' => $dr['Status'],
				'hdnThumbCrop' => $dr['ThumbCrop'],
				'chkVisibility' => $dr['SmallDesc'],
				'hdnCatIDs' => $dr['CatIDs'],
				'chkShowInHome' => $dr['ShowInHome'],
				'chkAdult' => $dr['Adult'],
				'ddlDividerLineType' => $dr['DividerLineType'],
				'txtContentSpacing' => $dr['ContentSpacing'],
				#
				'txtWorkFields' => $dr['WorkFields'],
				'txtTools' => $dr['Tools'],
				'txtTags' => $dr['Tags'],
				'txtSkills' => $dr['Skills'],
				#
				'hdnSchoolIDs' => $dr['SchoolIDs'],
				'txtSchools' => $dr['Schools'],
				'hdnCompanyIDs' => $dr['CompanyIDs'],
				'txtCompanies' => $dr['Companies'],
			);
			$this->attributes = $arrAttrs;
		}
	}

	#------------ Thumbnail --------------#

	const UploadPath = 'Projects/';

	public function getThumbID($GenerateNewOne = false, &$UniqueKey = null, $Refresh = false) {
		static $ID = null;
		if (!$ID || $Refresh || $GenerateNewOne) {
			if ($GenerateNewOne)
				$UniqueKey = uniqid(); //reference
			else {
				$dr = $this->getdtProjects($this->hdnID, $Refresh);
				if (!$dr || !$dr['Thumbnail'])
					return null;
			}
			$ID = self::UploadPath . $this->UserID . '_' . ($GenerateNewOne ? $UniqueKey : $dr['Thumbnail']);
		}
		return $ID;
	}

	public function getFreshThumbID(&$UnqID = null) {
		return $this->getThumbID(false, $UnqID, TRUE);
	}

//	public function UploadThumb() {
//		$this->scenario = 'Upload';
//		if (!$this->validate())
//			return false;
//		$this->_UploadThumb();
//	}

	private function _UploadThumb(&$CldR = null) {
		if ($this->fileThumb) {
			if ($this->ThumbID)
				$this->_DeleteThumb();
			$CldR = T\Cloudinary\Cloudinary::Uplaod($_FILES[$this->PostName]['tmp_name']['fileThumb']
							, array(
						'public_id' => $this->getThumbID("NEW", $PicUnqID)
							)
			);
			if ($CldR && $CldR['public_id'])
				return $PicUnqID;
			else {
				\Err::TraceMsg_Method(__METHOD__, "Cloudinary upload failed!", $CldR);
				$this->addError('fileThumb', \t2::site_site('Failed!'));
				return false;
			}
		}
		return null;
	}

	public function DeleteThumb() {
		$this->scenario = 'Edit';
		if (!$this->validate())
			return false;
		if ($this->_DeleteThumb()) {
			T\DB::Execute("UPDATE `_projects` SET `Thumbnail`=NULL, `ThumbnailCrop`=NULL WHERE `ID`=:id AND `UID`=:uid"
					, array(':id' => $this->hdnID, ':uid' => $this->UserID));
		}
	}

	private function _DeleteThumb() {
		$CldR = T\Cloudinary\Cloudinary::Destroy($this->ThumbID, array('invalidate' => true));
		if (!$CldR) {
			\Err::TraceMsg_Method(__METHOD__, "Cloudinary delete failed!", $CldR);
			$this->addError('fileThumb', \t2::site_site('Failed!'));
		}
		return $CldR;
	}

}
