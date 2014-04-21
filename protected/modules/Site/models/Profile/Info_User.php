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

	const OldestYearLimitation = 120;

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'txtBirthday',
			'ddlBirthdayFormat',
//			'filePicture',
		));
	}

	//--------- attrs
	public $txtBirthday;
	public $ddlBirthdayFormat = self::BDFormat_1;
	public $txtObjective;
	public $txtSmallDesc;
	public $txtDescription;

//	public $filePicture;	//avatar is a separated model and action

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
				'format' => C\Regexp::Yii_DateFormat_FullDigit,
				'except' => 'Delete'),
			array('ddlBirthdayFormat', 'required',
				'except' => 'Delete'),
			array('ddlBirthdayFormat', 'in',
				'range' => array_keys($this->arrBirthdayFormats),
				'except' => 'Delete'),
			array_merge(array('txtObjective', 'length',
				'except' => 'Delete'), $vl->UserObjective),
			array_merge(array('txtSmallDesc', 'length',
				'except' => 'Delete'), $vl->SmallDesc),
			array_merge(array('txtDescription', 'length',
				'except' => 'Delete'), $vl->Description),
				//mytodo 1 : filePicture (avatar of edit user info) - cloudinary
//			array_merge(array('filePicture', 'file',
//				'except' => 'Delete'), $vl->UserPicture),
		));
	}

	public function afterValidate($event) {
		if ($this->owner->scenario != 'Delete' && $this->txtBirthday &&
				preg_match(C\Regexp::DateFormat_FullDigit, $this->txtBirthday) &&
				strtotime($this->txtBirthday . ' +0000') > time())
			$this->owner->addError('txtBirthday', \t2::yii('{attribute} "{value}" is invalid.'
							, array('{attribute}' => $this->owner->getAttributeLabel('txtBirthday'), '{value}' => $this->txtBirthday)));
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'txtBirthday' => \t2::site_site('Birthday'),
			'ddlBirthdayFormat' => \t2::site_site('Birthday view'),
			'txtObjective' => \t2::site_site('Objective'),
			'txtSmallDesc' => \t2::site_site('Small description'),
			'txtDescription' => \t2::site_site('Description'),
//			'filePicture' => \t2::site_site('Picture'),
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
//						. ", `Picture`=:pic"
						. " ON DUPLICATE KEY UPDATE "
						. " `Birthday`=:birthday"
						. ", `BirthdayViewFormat`=:bdtype"
						. ", `Objective`=:objective"
						. ", `SmallDesc`=:smalldesc"
						. ", `Description`=:description"
//						. ", `Picture`=:pic"
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
//				'filePicture' => $drInfo->Picture,
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
