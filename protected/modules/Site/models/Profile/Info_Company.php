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
 * @property-read array $arrHowManyStaffs
 * @property-read array $arrOperatingStatuses
 * @property Info $owner
 */
class Info_Company extends \Base\FormModelBehavior {

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array(
			'ddlOperatingStatus',
			'ddlHowManyStaffs',
		));
	}

	public $txtCompanyTitle;
	public $txtCompanyURL;
	private $_CompanyDomain;
	public $ddlOperatingStatus;
	public $ddlHowManyStaffs;

	const OprStatus_Operating = 'Operating';
	const OprStatus_NotOperating = 'NotOperating';

	function getarrOperatingStatuses() {
		return array(
			self::OprStatus_Operating => self::OprStatus_Operating,
			self::OprStatus_NotOperating => self::OprStatus_NotOperating,
		);
	}

	function getarrHowManyStaffs() {
		return array(
			'1-10' => '1-10',
			'10-50' => '11-50',
			'50-200' => '51-200',
			'200-500' => '201-500',
			'500-5000' => '501-5000',
			'5000+' => '5000+',
		);
	}

	public function onBeforeRules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		$e->params['arrRules'] = array_merge($e->params['arrRules'], array(
			array_merge(array('txtCompanyTitle', 'length',
				'except' => 'Delete'), $vl->Title),
			array('txtCompanyURL', 'url',
				'except' => 'Delete'),
			array_merge(array('txtCompanyURL', 'length',
				'except' => 'Delete'), $vl->WebAddress),
			array('ddlOperatingStatus', 'in', 'range' => array_keys($this->arrOperatingStatuses),
				'except' => 'Delete'),
			array('ddlHowManyStaffs', 'in', 'range' => array_keys($this->arrHowManyStaffs),
				'except' => 'Delete'),
		));
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array(
			'txtCompanyTitle' => \t2::site_site('Title'),
			'txtCompanyURL' => \t2::site_site('Company web URL'),
			'ddlOperatingStatus' => \t2::site_site('Operating Status'),
			'ddlHowManyStaffs' => \t2::site_site('How Many Staffs'),
		));
	}

	function IsClaimedCompanyDomain($attr) {
		$owner = $this->owner;
		$PrmEmail = $owner->drUser->PrimaryEmail;
		if ($this->txtCompanyURL && $PrmEmail && preg_match(C\Regexp::Email, $PrmEmail)) {
			$Domain = explode('@', $PrmEmail);
			$Domain = $Domain[1];
			$URLDomain = parse_url($this->txtCompanyURL, PHP_URL_HOST);
			if (!preg_match(C\Regexp::CompanyURLDomain(preg_quote($Domain, '/')), $URLDomain))
				$owner->addError($attr, \t2::site_site("The url's domain doesn't match to your email domain"));
			else {
				$this->_CompanyDomain = $Domain;
				$drCompanyInfo = T\DB::GetRow(
								"SELECT `ID`, `OwnerUID`"
								. " FROM `_company_info`"
								. " WHERE `Domain`=:domain"
								, array(':domain' => $Domain));
				if ($drCompanyInfo &&
						$drCompanyInfo['OwnerUID'] &&
						$drCompanyInfo['OwnerUID'] != $owner->drUser->ID)
					$owner->addError($attr, \t2::site_site('Company has been claimed previously'));
			}
		}
	}

	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
		$owner = $this->owner;
		$owner->addTransactions(
				array(
					array(
						(!$owner->drInfo->CompanyInfoID ?
								"INSERT INTO `_company_info` SET"
								. " `OwnerUID`=:uid"
								. ", `Title`=:title"
								. ", `Domain`=:domain"
								. ", `URL`=:url"
								. ", `OperatingStatus`=:op_status"
								. ", `HowManyStaffs`=:hmstaffs" :
								"UPDATE `_company_info` SET"
								. " `Title`=:title"
								. ", `Domain`=:domain"
								. ", `URL`=:url"
								. ", `OperatingStatus`=:op_status"
								. ", `HowManyStaffs`=:hmstaffs"
								. " WHERE `OwnerUID`=>:uid"
						)
						, array(
							':uid' => $owner->drUser->ID,
							':title' => $this->txtCompanyTitle? : null,
							':domain' => $this->txtCompanyURL? : null,
							':url' => $this->txtCompanyURL? : null,
							':op_status' => $this->ddlOperatingStatus? : null,
							':hmstaffs' => $this->ddlHowManyStaffs? : null,
						)
					)
				)
		);
	}

	public function events() {
		return array_merge(parent::events(), array(
			'onSave' => 'onSave'
		));
	}

}
