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
 * @property-read array $arrGenders
 * @property-read array $arrHireAvailabilityTypes
 * @property Info $owner
 */
class Info_Artist extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'ddlGender',
			'chkTalentSearchVisibility',
			'chkRelocateInternally',
			'chkRelocateExternally',
			'txtHireAvailabilityDate',
			'ddlHireAvailabilityType',
		));
	}

	public $ddlGender;
	public $txtFirstName;
	public $txtLastName;
	public $txtMidName;
	public $txtArtistTitle;
	public $chkTalentSearchVisibility = 1;
	public $chkRelocateInternally = 0;
	public $chkRelocateExternally = 0;
	public $txtHireAvailabilityDate;
	public $ddlHireAvailabilityType;

	const Gender_Female = 'F';
	const Gender_Male = 'M';

	function getarrGenders() {
		return array(
			self::Gender_Female => \t2::site_site('Female'),
			self::Gender_Male => \t2::site_site('Man'),
		);
	}

	//Hire Availability Types (HAT)
	/** Hire Availability Type */
	const HAT_Full = 'Full';

	/** Hire Availability Type */
	const HAT_Part = 'Part';

	/** Hire Availability Type */
	const HAT_Intern = 'Intern';

	/** Hire Availability Type */
	const HAT_Contract = 'Contract';

	/** Hire Availability Type */
	const HAT_Home = 'Home';

	function getarrHireAvailabilityTypes() {
		return array(
			self::HAT_Contract => \t2::site_site(self::HAT_Contract),
			self::HAT_Full => \t2::site_site(self::HAT_Full),
			self::HAT_Home => \t2::site_site(self::HAT_Home),
			self::HAT_Intern => \t2::site_site(self::HAT_Intern),
			self::HAT_Part => \t2::site_site(self::HAT_Part),
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function onBeforeRules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		$e->params['arrRules'] = array_merge($e->params['arrRules'], array(
			array('ddlGender', 'in',
				'range' => array_keys($this->arrGenders),
				'except' => 'Delete'),
			array_merge(array('txtFirstName', 'length',
				'except' => 'Delete'), $vl->FirstName),
			array_merge(array('txtLastName', 'length',
				'except' => 'Delete'), $vl->LastName),
			array_merge(array('txtMidName', 'length',
				'except' => 'Delete'), $vl->MidName),
			array_merge(array('txtArtistTitle', 'length',
				'except' => 'Delete'), $vl->Title),
			array('chkTalentSearchVisibility, chkRelocateInternally, chkRelocateExternally', 'boolean',
				'except' => 'Delete'),
			array('txtHireAvailabilityDate', 'date',
				'format' => C\Regexp::Yii_DateFormat_FullDigit,
				'except' => 'Delete'),
			array('ddlHireAvailabilityType', 'in',
				'range' => array_keys($this->arrHireAvailabilityTypes),
				'except' => 'Delete'),
		));
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'ddlGender' => \t2::site_site('Gender'),
			'txtFirstName' => \t2::site_site('First name'),
			'txtLastName' => \t2::site_site('Last name'),
			'txtMidName' => \t2::site_site('Mid name'),
			'txtArtistTitle' => \t2::site_site('Title'),
			'chkTalentSearchVisibility' => \t2::site_site('Talent search visibility'),
			'chkRelocateInternally' => \t2::site_site('Relocate internally'),
			'chkRelocateExternally' => \t2::site_site('Relocate externally'),
			'txtHireAvailabilityDate' => \t2::site_site('Hire availability date'),
			'ddlHireAvailabilityType' => \t2::site_site('Hire availability type'),
		));
	}

	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
//		\Err::DebugBreakPoint($this->chkTalentSearchVisibility);
		$owner->addTransactions(
				array(
					array(
						"INSERT INTO `_artist_info` SET "
						. " `UID`=:uid"
						. ($this->owner->scenario == 'EditBasicInfo' ?
								", `Gender`=:gender"
								. ", `FirstName`=:fn"
								. ", `LastName`=:ln"
								. ", `MidName`=:mn"
								. ", `Title`=:title"
								. ", `TalentSearchVisibility`=:tsv" : "")
						. ($this->owner->scenario == 'EditAvailability' ?
								", `WouldRelocateInternally`=:ri"
								. ", `WouldRelocateExternally`=:re"
								. ", `HireAvailabilityDate`=:had"
								. ", `HireAvailabilityType`=:hat" : "")
						. " ON DUPLICATE KEY UPDATE "
						. ($this->owner->scenario == 'EditBasicInfo' ?
								" `Gender`=:gender"
								. ", `FirstName`=:fn"
								. ", `LastName`=:ln"
								. ", `MidName`=:mn"
								. ", `Title`=:title"
								. ", `TalentSearchVisibility`=:tsv" : "")
						. ($this->owner->scenario == 'EditAvailability' ?
								" `WouldRelocateInternally`=:ri"
								. ", `WouldRelocateExternally`=:re"
								. ", `HireAvailabilityDate`=:had"
								. ", `HireAvailabilityType`=:hat" : "")
						, array(
							':uid' => $owner->drUser->ID,
							':gender' => $this->ddlGender? : null,
							':fn' => $this->txtFirstName? : null,
							':ln' => $this->txtLastName? : null,
							':mn' => $this->txtMidName? : null,
							':title' => $this->txtArtistTitle? : null,
							':tsv' => $this->chkTalentSearchVisibility? : 0,
							':ri' => $this->chkRelocateInternally? : 0,
							':re' => $this->chkRelocateExternally? : 0,
							':had' => $this->txtHireAvailabilityDate? : null,
							':hat' => $this->ddlHireAvailabilityType? : null,
						)
					)
				)
		);
	}

	public function onSetForm(\CEvent $e) {
		$this->raiseEvent('onSetForm', $e);
		$owner = $this->owner;
		$drInfo = $owner->drInfo;
		$owner->attributes = array(
			'ddlGender' => $drInfo['Gender'],
			'txtFirstName' => $drInfo['FirstName'],
			'txtLastName' => $drInfo['LastName'],
			'txtMidName' => $drInfo['MidName'],
			'txtArtistTitle' => $drInfo['ArtistTitle'],
			'chkTalentSearchVisibility' => $drInfo['TalentSearchVisibility'],
			'chkRelocateInternally' => $drInfo['WouldRelocateInternally'],
			'chkRelocateExternally' => $drInfo['WouldRelocateExternally'],
			'txtHireAvailabilityDate' => $drInfo['HireAvailabilityDate'],
			'ddlHireAvailabilityType' => $drInfo['HireAvailabilityType'],
		);
	}

	public function events() {
		return array_merge(parent::events(), array(
			'onSave' => 'onSave',
			'onSetForm' => 'onSetForm',
		));
	}

}
