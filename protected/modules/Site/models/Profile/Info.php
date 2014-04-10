<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * SCENARIOS : addInfo / Edit / Delete / viewInfo / SetForm
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read UserDataRow $drUser Basic user data object of this user profile
 * @property-read InfoDataRow $drInfo More detailed data object of this user profile
 * -----User Behavior
 * @property-read array $arrBirthdayFormats birthday view formats
 * @property-read array $arrGenders
 * @property-read array $arrHireAvailabilityTypes
 * @property string $txtBirthday
 * @property string $ddlBirthdayFormat
 * @property string $txtObjective
 * @property string $txtSmallDesc
 * @property string $txtDescription
 * @property string $filePicture
 * -----Artist Behavior
 * @property string $ddlGender
 * @property string $txtFirstName
 * @property string $txtLastName
 * @property string $txtMidName
 * @property string $txtArtistTitle
 * @property string $chkTalentSearchVisibility
 * @property boolean $chkRelocateInternally
 * @property boolean $chkRelocateExternally
 * @property string $txtHireAvailabilityDate
 * @property string $ddlHireAvailabilityType
 * -----Company Behavior
 * @property string $txtCompanyTitle
 * @property string $txtCompanyURL
 * @property string $ddlOperatingStatus
 * @property string $ddlHowManyStaffs
 * -----Phones Behavior
 * @property integer $hdnContactID
 * @property-read array $arrPhoneTypes
 * @property-read array $dtContacts
 * @property-read array $dtFreshContacts
 * @method array getdtContacts(string $ContactID = NULL, boolean $refresh = false)
 * @property string $txtPhone
 * @property string $ddlPhoneType
 * //company contact
 * @property string $txtContactFirstName
 * @property string $txtContactLastName
 * @property string $txtContactJobTitle
 * -----Emails Behavior
 * @property string $hdnEmailID
 * @method array getdtEmails(string $EmailID = NULL, boolean $refresh = false)
 * @property-read array $dtEmails
 * @property-read array $dtFreshEmails
 * @property-read boolean $IsPrimaryEmailEdit
 * @property-read string $PendingEmail
 * @property-read string $ActivationEmail
 * @property-read string $ActivationCode
 * @method void ResetActivationLink()
 * @method void SetAsPrimary()
 * @property string $txtEmail
 * -----WebAddresses Behavior
 * @property-read array $arrWebAddrTypes
 * @property-read array $dtWebAddr
 * @property integer $hdnWebAddrID;
 * @property string $txtWebAddress
 * @property string $ddlWebAddrType
 * @property string $txtCustomType
 * -----Location Behavior
 * @property-read array $dtLocations
 * @property-read array $dtFreshLocations
 * @property-read array $drCurrentLocation
 * @property-read array $drBillingLocation
 * @property integer $hdnLocationID
 * @property string $ddlCountry
 * @property string $ddlDivision
 * @property string $ddlCity
 * @property string $txtCountry
 * @property string $txtDivision
 * @property string $txtCity
 * @property string $txtAddress1
 * @property string $txtAddress2
 * @property string $txtPostalCode
 * @property boolean $chkIsCurrentLocation
 * @property boolean $chkIsBillingLocation
 * -----Residencies Behavior
 * @property-read array $arrResidencyStatuses
 * @property-read array $dtResidencies
 * @property-read array $dtFreshResidencies
 * @property integer $hdnRsdCountryID
 * @property string $ddlCountry
 * @property string $txtCountry
 * @property string $rdoResidencyStatus
 * @property string $txtVisaType
 */
class Info extends \Base\FormModel_BehaviorHost {

	public function getPostName() {
		return "ProfileInfo";
	}

	public function getXSSPurification() {
		return $this->scenario === 'viewInfo' ? parent::getXSSPurification() : true;
	}

	//------------- behavior attachments
	public function Attach_Artist($Behavior = null) {
		if (!$Behavior)
			$Behavior = new Info_Artist();
		$this->attachBehavior('Info_Artist', $Behavior);
	}

	public function Attach_Company($Behavior = null) {
		if (!$Behavior)
			$Behavior = new Info_Company();
		$this->attachBehavior('Info_Company', $Behavior);
	}

	public function Attach_Emails($Behavior = null) {
		if (!$Behavior)
			$Behavior = new Info_Emails();
		$this->attachBehavior('Info_Emails', $Behavior);
	}

	public function Attach_Contacts($Behavior = null) {
		if (!$Behavior)
			$Behavior = new Info_Contacts();
		$this->attachBehavior('Info_Contacts', $Behavior);
	}

	public function Attach_Locations($Behavior = null) {
		if (!$Behavior)
			$Behavior = new Info_Locations();
		$this->attachBehavior('Info_Locations', $Behavior);
	}

	public function Attach_Residencies($Behavior = null) {
		if (!$Behavior)
			$Behavior = new Info_Residencies();
		$this->attachBehavior('Info_Residencies', $Behavior);
	}

	public function Attach_User($Behavior = null) {
		if (!$Behavior)
			$Behavior = new Info_User();
		$this->attachBehavior('Info_User', $Behavior);
	}

	public function Attach_WebAddresses($Behavior = null) {
		if (!$Behavior)
			$Behavior = new Info_WebAddresses();
		$this->attachBehavior('Info_WebAddresses', $Behavior);
	}

	//------------- User check and datarow
	public $Username;

	/**
	 * checks username and user account and gets user basic data row of _users datatable
	 * @staticvar null $drUser
	 * @param string $Username
	 * @return UserDataRow
	 * @throws \Err
	 * @throws \CHttpException
	 */
	function getdrUser($Username = null) {
		static $drUser = null;
		if ($drUser && $Username && $Username !== $this->Username)
			throw new \Err(__METHOD__, 'You can set Username only once', func_get_args());
		if (!$drUser) {
			if ($Username)
				$this->Username = $Username;
			$Username = $this->Username;
			if (!$Username || !preg_match(C\Regexp::Username, $Username) ||
					preg_match(C\Regexp::Username_InvalidCases(), $Username))
				throw new \CHttpException(404, \t2::Site_Common('Requested profile was not found'));

			$drUser = T\DB::Query(
							"SELECT"
							. " u.`ID`"
							. ", u.`ParentAccountID`"
							. ", u.`AccountType`"
							. ", ut.`Title` AS UserType"
							. ", u.`Username`"
							. ", u.`PrimaryEmail`"
							. " FROM `_users` u"
							. " INNER JOIN (SELECT 1) tmp ON u.`Username`=:un AND u.`Status`=:active"
							. " LEFT JOIN `_user_types` ut ON u.`UTypeExpDate`>UTC_DATE() AND ut.`IsActive` AND ut.`ID`=u.`UserTypeID`"
							, array(
						':un' => $Username,
						':active' => C\User::Status_Active
							)
			);
			if (!$drUser || !$drUser->count())
				throw new \CHttpException(404, \t2::Site_Common('Requested profile was not found'));
			$drUser = $drUser->readObject('\Site\models\Profile\UserDataRow', array());
		}
		return $drUser;
	}

	//Profile info
	public function getdrInfo() {
		if (!($drUser = $this->drUser))
			return false;
		//Behaviors binary check
		static $AttachedBehaviors = null;
		$Info_User = $this->asa('Info_User');
		$Info_Artist = $this->asa('Info_Artist');
		$Info_Company = $this->asa('Info_Company');
		if (!$Info_User && !$Info_Artist && !$Info_Company)
			throw new \Err(__METHOD__, "No behavior has been attached!");
		$CurrentAttachedBehaviors = ($Info_User ? 2 : 0) |
				($Info_Artist ? 4 : 0) |
				($Info_Company ? 8 : 0);
		static $drInfo = null;
		if (!$drInfo || $AttachedBehaviors !== $CurrentAttachedBehaviors) {
			$AttachedBehaviors = $CurrentAttachedBehaviors;
			$drInfo = T\DB::Query(
							"SELECT tmp._tmpCol"//tmpCol to make relax about Commas
							. ($Info_User ?
									", ui.`Birthday`"
									. ", ui.BirthdayViewFormat"
									. ", ui.Objective"
									. ", ui.SmallDesc"
									. ", ui.Description"
									. ", ui.Picture" : ""
							)
							. ($Info_Artist ?
									", ai.`Gender`"
									. ", ai.FirstName"
									. ", ai.LastName"
									. ", ai.MidName"
									. ", ai.Title AS ArtistTitle"
									. ", ai.TalentSearchVisibility"
									. ", ai.WouldRelocateInternally"
									. ", ai.WouldRelocateExternally"
									. ", ai.HireAvailabilityDate"
									. ", ai.HireAvailabilityType" : ""
							)
							. ($Info_Company ?
									", ci.`ID` AS CompanyInfoID"
									. ", ci.Title AS CompanyTitle"
									. ", ci.Domain AS CompanyDomain"
									. ", ci.URL AS CompanyURL"
									. ", ci.Verified AS VerifiedCompany"
									. ", ci.OperatingStatus"
									. ", ci.HowManyStaffs" : ""
							)
							. " FROM (SELECT 1 _tmpCol) tmp"
							. ($Info_User ?
									" LEFT JOIN `_user_info` ui ON ui.UID=:uid" : "")
							. ($Info_Artist ?
									" LEFT JOIN `_artist_info` ai ON ai.UID=:uid" : "")
							. ($Info_Company ?
									" LEFT JOIN `_company_info` ci ON ci.OwnerUID=:uid" : "")
							, array(
						':uid' => $drUser->ID,
							)
			);
			$drInfo = $drInfo->readObject('\Site\models\Profile\InfoDataRow', array());
		}
		return $drInfo;
	}

	//------------- Save transactions
	private $_arrTransactions = array();

	function addTransactions($arrNewTransactions) {
		$this->_arrTransactions = array_merge($this->_arrTransactions, $arrNewTransactions);
	}

	private function RunTransactions() {
		$Result = T\DB::Transaction(array_values($this->_arrTransactions)) ? true : false;
		$this->_arrTransactions = array();
		return $Result;
	}

	public function onSave(\CEvent $e) {
		$this->raiseEvent('onSave', $e);
	}

	public function Save() {
		if (!$this->validate())
			return false;
		$this->onSave(new \CEvent($this));
		if ($Result = $this->RunTransactions())
			$this->scenario = 'Edit';
		return $Result;
	}

	public function onSetForm(\CEvent $e) {
		$this->raiseEvent('onSetForm', $e);
	}

	public function SetForm() {
		$this->onSetForm(new \CEvent($this));
	}

	public function onDelete(\CEvent $e) {
		$this->raiseEvent('onDelete', $e);
	}

	/**
	 * Delete is only for special behaviors such as Contact or location items
	 * @return boolean
	 */
	public function Delete() {
		$this->scenario = 'Delete';
		if (!$this->validate())
			return false;
		$this->onDelete(new \CEvent($this));
		if ($Result = $this->RunTransactions())
			$this->scenario = 'Add';
		return $Result;
	}

}

/**
 * @property-read integer $ID
 * @property-read integer $ParentAccountID
 * @property-read string $AccountType
 * @property-read string $UserType
 * @property-read string $Username
 * @property-read stirng $PrimaryEmail
 */
class UserDataRow extends \Base\ConfigArray {
	
}

/**
 * ----- user information
 * @property-read string $Birthday
 * @property-read string $BirthdayViewFormat
 * @property-read string $Objective
 * @property-read string $SmallDesc
 * @property-read string $Description
 * @property-read string $Picture
 * ----- artist information
 * @property-read string $Gender
 * @property-read string $FirstName
 * @property-read string $LastName
 * @property-read string $MidName
 * @property-read string $ArtistTitle
 * @property-read boolean $TalentSearchVisibility
 * @property-read boolean $WouldRelocateInternally
 * @property-read boolean $WouldRelocateExternally
 * @property-read string $HireAvailabilityDate
 * @property-read string $HireAvailabilityType
 * ----- company information
 * @property-read integer $CompanyInfoID
 * @property-read string $CompanyTitle
 * @property-read string $CompanyDomain
 * @property-read string $CompanyURL
 * @property-read boolean $VerifiedCompany
 * @property-read string $OperatingStatus
 * @property-read string $HowManyStaffs
 */
class InfoDataRow extends \Base\ConfigArray {
	
}
