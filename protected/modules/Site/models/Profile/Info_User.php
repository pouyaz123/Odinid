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
 * @property-write event $onSave push on save event handlers here
 * @property-read array $arrBirthdayFormats birthday view formats
 * @property-read array $arrGenders
 * @property-read array $arrHireAvailabilityTypes
 * @property Info $owner
 */
class Info_User extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'txtBirthday',
			'ddlBirthdayFormat',
			'filePicture',
		));
	}

	//--------- attrs
	public $txtBirthday;
	public $ddlBirthdayFormat = self::BDFormat_1;
	public $txtObjective;
	public $txtSmallDesc;
	public $txtDescription;
	public $filePicture;

	const BDFormat_1 = 'mm/dd/yy';
	const BDFormat_2 = 'dd/mm/yy';

	function getarrBirthdayFormats() {
		return array(
			self::BDFormat_1 => self::BDFormat_1,
			self::BDFormat_2 => self::BDFormat_2,
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function onBeforeRules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		$e->params['arrRules'] = array_merge($e->params['arrRules'], array(
			array('txtBirthday', 'date',
				'format' => C\Regexp::DateFormat_Yii_FullDigit,
				'except' => 'Delete'),
			array('ddlBirthdayFormat', 'required',
				'except' => 'Delete'),
			array('ddlBirthdayFormat', 'in',
				'range' => array_keys($this->arrBirthdayFormats),
				'except' => 'Delete'),
			array_merge(array('txtObjective', 'length',
				'except' => 'Delete'), $vl->UserObjective),
			array_merge(array('txtSmallDesc', 'length',
				'except' => 'Delete'), $vl->UserSmallDesc),
			array_merge(array('txtDescription', 'length',
				'except' => 'Delete'), $vl->UserDescription),
				//mytodo 1 : filePicture (avatar of edit user info) - cloudinary
//			array_merge(array('filePicture', 'file',
//				'except' => 'Delete'), $vl->UserPicture),
		));
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'txtBirthday' => \t2::Site_User('Birthday'),
			'ddlBirthdayFormat' => \t2::Site_User('Birthday view'),
			'txtObjective' => \t2::Site_User('Objective'),
			'txtSmallDesc' => \t2::Site_Common('Small description'),
			'txtDescription' => \t2::Site_Common('Description'),
			'filePicture' => \t2::Site_Common('Picture'),
		));
	}

	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		$owner->addTransactions(
				array(
					array(
						"INSERT INTO `_user_info` SET "
						. " `UID`=:uid"
						. ", `Birthday`=:birthday"
						. ", `BirthdayViewFormat`=:bdtype"
						. ", `Objective`=:objective"
						. ", `SmallDesc`=:smalldesc"
						. ", `Description`=:description"
						. ", `Picture`=:pic"
						. " ON DUPLICATE KEY UPDATE "
						. " `Birthday`=:birthday"
						. ", `BirthdayViewFormat`=:bdtype"
						. ", `Objective`=:objective"
						. ", `SmallDesc`=:smalldesc"
						. ", `Description`=:description"
						. ", `Picture`=:pic"
						, array(
							':uid' => $owner->drUser->ID,
							':birthday' => $this->txtBirthday? : null,
							':bdtype' => $this->ddlBirthdayFormat,
							':objective' => $this->txtObjective? : null,
							':smalldesc' => $this->txtSmallDesc? : null,
							':description' => $this->txtDescription? : null,
							':pic' => null, //mytodo 1 : R&D for cloudinary to upload and put pic here in info
						)
					)
				)
		);
	}

	public function onSetForm(\CEvent $e) {
		$this->raiseEvent('onSetForm', $e);
		$owner = $this->owner;
		if ($drInfo = $owner->drInfo) {
			$arrAttrs = array(
				'txtBirthday' => $drInfo->Birthday,
				'ddlBirthdayFormat' => $drInfo->BirthdayViewFormat,
				'txtObjective' => $drInfo->Objective,
				'txtSmallDesc' => $drInfo->SmallDesc,
				'txtDescription' => $drInfo->Description,
				'filePicture' => $drInfo->Picture,
			);
			$owner->attributes = $arrAttrs;
		}
	}

	public function events() {
		return array_merge(parent::events(), array(
			'onSave' => 'onSave',
			'onSetForm' => 'onSetForm',
		));
	}

}
