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
 * @property-read array|null $drAvatar
 * @property-read string $AvatarID
 * @property-read string $FreshAvatarID
 */
class Avatar extends \Base\FormModel {

	public function getPostName() {
		return "UserAvatar";
	}

	public function getXSSPurification() {
		return false;
	}

	//----- attrs
	public $fileAvatar;
	public $hdnCropDims;

#

	public $UserID;

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array_merge(array('fileAvatar', 'file',
				'on' => 'Upload'), $vl->UserPicture),
//			array('hdnCropDims', 'required',
//				'on' => 'Crop'),
			array('hdnCropDims', 'match', 'pattern' => C\Regexp::CropDims),
		);
	}

	protected function afterValidate() {
		$this->CheckUserID();
		if (($this->scenario == 'Delete' || $this->scenario == 'Crop') && !$this->AvatarID)
			$this->addError('', '');
	}

	private function CheckUserID() {
		if (!$this->UserID)
			throw new \Err(__METHOD__, "UserID has not been set!");
	}

	public function attributeLabels() {
		return array(
			'fileAvatar' => \t2::site_site('Avatar'),
		);
	}

	public function Upload() {
		$this->scenario = 'Upload';
		if (!$this->validate())
			return false;
		if ($this->AvatarID)
			$this->Delete();
		$CldR = \Cloudinary\Uploader::upload($_FILES[$this->PostName]['tmp_name']['fileAvatar']
						, array(
					'public_id' => $this->getAvatarID("NEW", $PicUnqID, false, 1)
						)
		);
		if ($CldR && $CldR['public_id']) {
			T\DB::Execute("INSERT INTO `_user_info`(`UID`, `PictureUnique`)"
					. " VALUES(:uid, :picunq) ON DUPLICATE KEY UPDATE `PictureUnique`=:picunq"
					, array(':uid' => $this->UserID, ':picunq' => $PicUnqID)
			);
		} else {
			\Err::TraceMsg_Method(__METHOD__, "Cloudinary upload failed!", $CldR);
			$this->addError('fileAvatar', \t2::site_site('Failed!'));
		}
	}

	public function Delete() {
		if (!$this->validate() || !$this->AvatarID)
			return false;
		$CldR = \Cloudinary\Uploader::destroy($this->AvatarID, array('invalidate' => true));
		if ($CldR) {
			T\DB::Execute("UPDATE `_user_info` SET `PictureUnique`=NULL, `PictureCoords`=NULL WHERE `UID`=:uid"
					, array(':uid' => $this->UserID));
		} else {
			\Err::TraceMsg_Method(__METHOD__, "Cloudinary delete failed!", $CldR);
			$this->addError('fileAvatar', \t2::site_site('Failed!'));
		}
	}

	public function Crop() {
		$this->scenario = 'Crop';
		if (!$this->validate() || !$this->AvatarID)
			return false;
		T\DB::Execute("INSERT INTO `_user_info`(`UID`, `PictureCoords`)"
				. " VALUES(:uid, :coords)"
				. " ON DUPLICATE KEY UPDATE"
				. " `PictureCoords`=:coords"
				, array(
			':uid' => $this->UserID,
			':coords' => $this->hdnCropDims? : null,
		));
	}

	public function getdrAvatar($Refresh = false) {
		static $dr = null;
		if (!$dr || $Refresh) {
			$this->CheckUserID();
			$dr = T\DB::GetRow("SELECT `UID`, `PictureUnique`, `PictureCoords` FROM `_user_info` WHERE `UID`=:uid"
							, array(':uid' => $this->UserID));
		}
		return $dr;
	}

	const UploadPath = 'Avatars/';

	public function getAvatarID($GenerateNewOne = false, &$UniqueKey = null, $Refresh = false) {
		static $ID = null;
		if (!$ID || $Refresh || $GenerateNewOne) {
			if ($GenerateNewOne)
				$UniqueKey = uniqid(); //reference
			else {
				$dr = $this->getdrAvatar($Refresh);
				if (!$dr || !$dr['PictureUnique'])
					return null;
			}
			$ID = self::UploadPath . $this->UserID . '_' . ($GenerateNewOne ? $UniqueKey : $dr['PictureUnique']);
		}
		return $ID;
	}

	public function getFreshAvatarID(&$UnqID = null) {
		return $this->getAvatarID(false, $UnqID, TRUE);
	}

	public function SetForm() {
		$dr = $this->getdrAvatar();
		if ($dr) {
			$arrAttrs = array(
				'hdnCropDims' => $dr['PictureCoords'],
			);
			$this->attributes = $arrAttrs;
		}
	}
}
