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
 */
class Project extends \Base\FormModel {

	public function getPostName() {
		return "Prj";
	}

	protected function XSSPurify_Exceptions() {
		return "hdnID";
	}

	//----- attrs
	public $hdnID;
	#
	public $txtTitle;
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
				'on' => 'Edit, Delete'),
			array('hdnID', 'IsExist',
				'SQL' => 'SELECT COUNT(*) FROM `_projects` WHERE `ID`=:val AND `UID`=:uid AND `Type`=:type',
				'SQLParams' => array(':uid' => $this->UserID, ':type' => $this->Type),
				'on' => 'Edit, Delete'),
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
				'on' => 'Upload'), $vl->ProjectThumb),
			array('hdnThumbCrop', 'match',
				'pattern' => C\Regexp::CropDims,
				'on' => 'Crop'),
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
		$this->_ValidationPassed = true;
		if (!$this->hdnID) {//means in add mode not edit mode
			$Count = T\DB::GetField("SELECT COUNT(*) FROM `_project_cats` WHERE `UID`=:uid"
							, array(':uid' => $this->UserID));
			if ($Count && $Count >= T\Settings::GetInstance()->MaxProjectCats)
				$this->addError('', \t2::site_site('You have reached the maximum'));
		}
		$v = \ValidationLimits\User::GetInstance();
		$fncMassLenVld = function ($MassField, $vldField, $arrVldConf) {
			$Items = $this->$MassField;
			if ($Items) {
				$vld = new \CStringValidator();
				$vld->on = array('Add', 'Edit');
				$vld->attributes = array($vldField);
				T\Basics::ConfigureObject($vld, $arrVldConf);

				$Items = trim($Items, ",\t\n\r\0\x0B");
				$arrItems = explode(',', $Items);
				foreach ($arrItems as $Item) {
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

	public function UploadThumbnail() {
		$this->scenario = 'Upload';
		if (!$this->validate())
			return false;
		$PicUnqID = null;
		if ($this->fileThumb) {
			$CldR = T\Cloudinary\Cloudinary::Uplaod($_FILES[$this->PostName]['tmp_name']['fileThumb']
							, array(
						'public_id' => $this->getThumbID("NEW", $PicUnqID, false, 1)
							)
			);
		}
	}

	public function Upload() {
		$this->scenario = 'Upload';
		if (!$this->validate())
			return false;
		if ($this->AvatarID)
			$this->Delete();
		$CldR = T\Cloudinary\Cloudinary::Uplaod($_FILES[$this->PostName]['tmp_name']['fileAvatar']
						, array(
					'public_id' => $this->getAvatarID("NEW", $PicUnqID, false, 1)
						)
		);
		if ($CldR && $CldR['public_id']) {
			T\DB::Execute("INSERT INTO `_user_info`(`UID`, `Picture`)"
					. " VALUES(:uid, :picunq) ON DUPLICATE KEY UPDATE `Picture`=:picunq"
					, array(':uid' => $this->UserID, ':picunq' => $PicUnqID)
			);
		} else {
			\Err::TraceMsg_Method(__METHOD__, "Cloudinary upload failed!", $CldR);
			$this->addError('fileAvatar', \t2::site_site('Failed!'));
		}
	}

	public function Save() {
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
		$Result = T\DB::Execute(
						!$this->hdnID ?
								"INSERT INTO `_projects`("
								. "`ID`, `UID`, `Type`"
								. ", `Title`, `SmallDesc`"
								. ", `IsReel`, `PaidTutorial`, `Status`"
//								. ", `Thumbnail`"
								. ", `ThumbnailCrop`"
								. ", `Visibility`, `ShowInHomePage`, `Adult`"
								. ", `Password`, `DividerLineType`, `ContentSpacing`"
								. ") VALUES("
								. " :id, :uid, :type"
								. ", :ttl, :smldsc"
								. ", :reel, :paidtut, :status"
								. ", :thumb, :thumbcrop"
								. ", :vsblty, :home, :adult"
								. ", :pwd, :dvdr, :cntspc)" :
								"UPDATE `_projects` SET"
								. " `Title`=:ttl, `SmallDesc`=:smldsc"
								. ", `IsReel`=:reel, `PaidTutorial`=:paidtut, `Status`=:status"
//								. ", `Thumbnail`=:thumb"
								. ", `ThumbnailCrop`=:thumbcrop"
								. ", `Visibility`=:vsblty, `ShowInHomePage`=:home, `Adult`=:adult"
								. ", `Password`=:pwd, `DividerLineType`=:dvdr, `ContentSpacing`=:cntspc"
								. " WHERE `ID`=:id AND `UID`=:uid AND `Type`=:type"
						, array(
					':id' => $ID,
					':uid' => $this->UserID,
					':type' => $this->Type,
					':ttl' => $this->txtTitle,
					':smldsc' => $this->txtSmallDesc? : null,
					':reel' => $this->chkIsReel? : 0,
					':paidtut' => $this->chkPaidTutorial? : 0,
					':status' => $this->ddlStatus,
//					':thumb' => $PicUnqID,
					':thumbcrop' => $this->hdnThumbCrop? : null,
					':vsblty' => $this->chkVisibility? : 0,
					':home' => $this->chkShowInHome? : 0,
					':adult' => $this->chkAdult? : 0,
					':pwd' => $this->txtPassword? : null,
					':dvdr' => $this->ddlDividerLineType? : null,
					':cntspc' => $this->txtContentSpacing? : null,
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
		$Result = T\DB::Execute("DELETE FROM `_project_cats` WHERE `ID`=:id AND `UID`=:uid"
						, array(
					':id' => $this->hdnID,
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
				$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_project_cats` WHERE `UID`=:uid'
								, array(':uid' => $this->UserID));
				$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
			}
			$arrDTs[$StaticIndex] = T\DB::GetTable(
							"SELECT *"
							. " FROM `_project_cats`"
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
		$dr = $this->getdtProjects($this->hdnID);
		if ($dr) {
			$dr = $dr[0];
			$arrAttrs = array(
				'hdnID' => $dr['ID'],
				'txtTitle' => $dr['Title'],
			);
			$this->attributes = $arrAttrs;
		}
	}

	const UploadPath = 'Projects/';

	public function getThumbID($GenerateNewOne = false, &$UniqueKey = null, $Refresh = false) {
		static $ID = null;
		if (!$ID || $Refresh || $GenerateNewOne) {
			if ($GenerateNewOne)
				$UniqueKey = uniqid(); //reference
			else {
				$dr = $this->getdrAvatar($Refresh);
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

}
