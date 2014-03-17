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
	public $chkTalentSearchVisibility;
	public $chkRelocateInternally;
	public $chkRelocateExternally;
	public $txtHireAvailabilityDate;
	public $ddlHireAvailabilityType;

	const Gender_Female = 'F';
	const Gender_Male = 'M';

	function getarrGenders() {
		return array(
			self::Gender_Female => \t2::Site_Artist('Female'),
			self::Gender_Male => \t2::Site_Artist('Man'),
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
			self::HAT_Contract => \t2::Site_Artist(self::HAT_Contract),
			self::HAT_Full => \t2::Site_Artist(self::HAT_Full),
			self::HAT_Home => \t2::Site_Artist(self::HAT_Home),
			self::HAT_Intern => \t2::Site_Artist(self::HAT_Intern),
			self::HAT_Part => \t2::Site_Artist(self::HAT_Part),
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
				'format' => C\Regexp::DateFormat_Yii_FullDigit,
				'except' => 'Delete'),
			array('ddlHireAvailabilityType', 'in',
				'range' => array_keys($this->arrHireAvailabilityTypes),
				'except' => 'Delete'),
		));
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'ddlGender' => \t2::Site_Artist('Gender'),
			'txtFirstName' => \t2::Site_Artist('First name'),
			'txtLastName' => \t2::Site_Artist('Last name'),
			'txtMidName' => \t2::Site_Artist('Mid name'),
			'txtArtistTitle' => \t2::Site_Common('Title'),
			'chkTalentSearchVisibility' => \t2::Site_Artist('Talent search visibility'),
			'chkRelocateInternally' => \t2::Site_Artist('Relocate internally'),
			'chkRelocateExternally' => \t2::Site_Artist('Relocate externally'),
			'txtHireAvailabilityDate' => \t2::Site_Artist('Hire availability date'),
			'ddlHireAvailabilityType' => \t2::Site_Artist('Hire availability type'),
		));
	}

	public function events() {
		return array_merge(parent::events(), array(
			'onSave' => 'onSave',
			'onSetForm' => 'onSetForm',
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
						. ", `Gender`=:gender"
						. ", `FirstName`=:fn"
						. ", `LastName`=:ln"
						. ", `MidName`=:mn"
						. ", `Title`=:title"
						. ", `TalentSearchVisibility`=:tsv"
						. ", `WouldRelocateInternally`=:ri"
						. ", `WouldRelocateExternally`=:re"
						. ", `HireAvailabilityDate`=:had"
						. ", `HireAvailabilityType`=:hat"
						. " ON DUPLICATE KEY UPDATE "
						. " `Gender`=:gender"
						. ", `FirstName`=:fn"
						. ", `LastName`=:ln"
						. ", `MidName`=:mn"
						. ", `Title`=:title"
						. ", `TalentSearchVisibility`=:tsv"
						. ", `WouldRelocateInternally`=:ri"
						. ", `WouldRelocateExternally`=:re"
						. ", `HireAvailabilityDate`=:had"
						. ", `HireAvailabilityType`=:hat"
						, array(
							':uid' => $owner->drUser->ID,
							':gender' => $this->ddlGender? : null,
							':fn' => $this->txtFirstName? : null,
							':ln' => $this->txtLastName? : null,
							':mn' => $this->txtMidName? : null,
							':title' => $this->txtArtistTitle? : null,
							':tsv' => $this->chkTalentSearchVisibility? : null,
							':ri' => $this->chkRelocateInternally? : null,
							':re' => $this->chkRelocateExternally? : null,
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

}
