<?php

namespace Site\models\User;

use \Consts as C;
use \Tools as T;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read array $arrAccountTypes
 * @property-read string $ActivationCode the activation code after a registration process
 * @method boolean Register() //callRegister + relative events
 */
class Register extends \Base\FormModel {

	public function getPostName() {
		return 'Register';
	}

	protected function XSSPurify_Exceptions() {
		return 'ddlAccountType, txtPassword, txtEmailRepeat, txtCaptcha, txtInvitationCode';
	}

	//attrs
	public $ddlAccountType = self::UserAccountType_Artist;
	public $txtEmail;
	public $txtEmailRepeat;
	public $txtUsername;
	public $txtPassword;
	public $txtCaptcha;
	//artist
	public $txtInvitationCode;
	//company
	public $txtCompanyURL = false;
	//geolocations
	public $ddlCountry;
	public $ddlDivision;
	public $ddlCity;
	public $txtCountry;
	public $txtDivision;
	public $txtCity;
//	public $txtAddress1 = '';
//	public $txtAddress2 = '';
	#
	private $_CompanyDomain = NULL;
	private $_drCompanyInfo = null;
	private $_drUserType = NULL;

	const UserAccountType_Artist = C\User::UserAccountType_Artist;
	const UserAccountType_Company = C\User::UserAccountType_Company;

	public function getArrAccountTypes() {
		return array(
			self::UserAccountType_Artist => self::UserAccountType_Artist,
			self::UserAccountType_Company => self::UserAccountType_Company,
		);
	}

	private $_ActivationCode = null;

	function getActivationCode() {
		return $this->_ActivationCode;
	}

	protected function CleanViewStateOfSpecialAttrs() {
		$this->txtPassword = $this->txtCaptcha = null;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('txtEmail, txtEmailRepeat, txtUsername, txtPassword, txtCaptcha', 'required'),
			array('ddlAccountType', 'in', 'range' => array_keys($this->arrAccountTypes)),
			array('txtCaptcha', 'MyCaptcha'),
			#un pw
			array('txtUsername', 'match', 'pattern' => C\Regexp::Username),
			array('txtUsername', 'match', 'not' => true, 'pattern' => C\Regexp::Username_InvalidCases()),
			array_merge(array('txtUsername', 'length'), $vl->Username),
			array('txtUsername', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_users` WHERE `Username`=:val LIMIT 1'),
			array_merge(array('txtPassword', 'length'), $vl->Password),
			#email
			array('txtEmail', 'email'),
			array_merge(array('txtEmail', 'length'), $vl->Email),
			array('txtEmailRepeat', 'compare',
				'compareAttribute' => 'txtEmail'),
			array('txtEmail', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_user_emails` WHERE `Email`=:val'),
			#artist
			array('txtInvitationCode', 'required',
				'on' => 'ArtistRegister'),
			array_merge(array('txtInvitationCode', 'length'
				, 'on' => 'ArtistRegister'), $vl->InvitationCode),
			array('txtInvitationCode', 'IsValidInvitation',
				'on' => 'ArtistRegister'),
			#company
			array('ddlCountry, ddlDivision, ddlCity, txtCountry, txtDivision, txtCity', 'required',
				'on' => 'CompanyRegister'),
			array('txtCompanyURL', 'url',
				'on' => 'CompanyRegister'),
			array_merge(array('txtCompanyURL', 'length',
				'on' => 'CompanyRegister'), $vl->WebAddress),
			array('txtCompanyURL', 'IsClaimedCompanyDomain',
				'on' => 'CompanyRegister'),
			#location
			array('ddlCountry, ddlDivision, ddlCity, txtCountry, txtDivision, txtCity', 'match',
				'pattern' => C\Regexp::SimpleWords,
				'on' => 'CompanyRegister'),
			array_merge(array('ddlCountry, txtCountry', 'length',
				'on' => 'CompanyRegister'), $vl->Country),
			array_merge(array('ddlDivision, txtDivision', 'length',
				'on' => 'CompanyRegister'), $vl->Division),
			array_merge(array('ddlCity, txtCity', 'length',
				'on' => 'CompanyRegister'), $vl->City),
		);
	}

	public function attributeLabels() {
		return array(
			'ddlAccountType' => \t2::Site_User('Account type'),
			'txtEmail' => \t2::Site_User('Email'),
			'txtEmailRepeat' => \t2::Site_User('Confirm email'),
			'txtUsername' => \t2::Site_User('Username'),
			'txtPassword' => \t2::Site_User('Password'),
			'txtCaptcha' => \t2::General('Captcha code'),
			#artist
			'txtInvitationCode' => \t2::Site_User('Invitation code'),
			#Company
			'txtCompanyURL' => \t2::Site_Company('Company web URL'),
			#location
			'ddlCountry' => \t2::Site_Common('Country'),
			'ddlDivision' => \t2::Site_Common('Division'),
			'ddlCity' => \t2::Site_Common('City'),
			'txtCountry' => \t2::Site_Common('Country'),
			'txtDivision' => \t2::Site_Common('Division'),
			'txtCity' => \t2::Site_Common('City'),
//			'txtAddress1' => \t2::Site_Common('Address 1'),
//			'txtAddress2' => \t2::Site_Common('Address 2'),
		);
	}

	function IsValidInvitation() {
		$this->_drUserType = T\DB::GetRow(
						"SELECT `UserTypeID`, `UserTypeExpDate`"
						. " FROM `_user_invitations`"
						. " WHERE `Code`=:code"
						. " AND (ISNULL(`InvitationExpDate`) OR `InvitationExpDate`='' OR `InvitationExpDate`>UTC_DATE())"
						, array(':code' => $this->txtInvitationCode));
		if (!$this->_drUserType)
			$this->addError('txtInvitationCode', \t2::Site_User('Invalid invitation code'));
	}

	function IsClaimedCompanyDomain($attr) {
		if ($this->txtCompanyURL && $this->txtEmail && preg_match(C\Regexp::Email, $this->txtEmail)) {
			$Domain = explode('@', $this->txtEmail);
			$Domain = $Domain[1];
			$URLDomain = parse_url($this->txtCompanyURL, PHP_URL_HOST);
			if (!preg_match(C\Regexp::CompanyURLDomain(preg_quote($Domain, '/')), $URLDomain))
				$this->addError($attr, \t2::Site_Company("The url's domain doesn't match to your email domain"));
			else {
				$this->_CompanyDomain = $Domain;
				$this->_drCompanyInfo = T\DB::GetRow(
								"SELECT `ID`, `OwnerUID`"
								. " FROM `_company_info`"
								. " WHERE `Domain`=:domain"
								, array(':domain' => $Domain));
				if ($this->_drCompanyInfo && $this->_drCompanyInfo['OwnerUID'])
					$this->addError($attr, \t2::Site_Company('Company has been claimed previously'));
			}
		}
	}

	function onAfterRegister() {
		$this->CleanViewStateOfSpecialAttrs();
	}

	/**
	 * use ->Register() instead to trigger relative important events
	 * @return boolean
	 */
	function callRegister() {
		if (!$this->validate())
			return false;

		$this->_ActivationCode = T\DB::GetUniqueCode('_user_recoveries', 'Code');
		$PrimaryUserID = Login::GetSessionDR('ID');
		$IsCompany = ($this->ddlAccountType == self::UserAccountType_Company);
		$drUserType = &$this->_drUserType;
		$strSQLPart_UserType = $drUserType ?
				":utid" :
				"SELECT `ID` FROM `_user_types` WHERE `IsDefault`";
		$Queries = array();
		$CommonParams = array(
			':un' => $this->txtUsername,
		);
		$Queries[] = array("INSERT INTO `_users`(`ParentAccountID`, `AccountType`, `UserTypeID`, `UTypeExpDate`, `Username`, `Password`, `PendingPrimaryEmail`, `RegisterDateTime`)"
			. " VALUES(:parentid, :accounttype, ($strSQLPart_UserType), :utexp, :un, :pw, :email, :registertime)",
			array(
				':parentid' => $PrimaryUserID,
				':accounttype' => $this->ddlAccountType,
				':utid' => $drUserType ? $drUserType['UserTypeID'] : NULL,
				':utexp' => $drUserType ? $drUserType['UserTypeExpDate'] : NULL,
				':pw' => T\UserAuthenticate::Crypt($this->txtPassword),
				':registertime' => gmdate('Y-m-d H:i:s'),
				':email' => $this->txtEmail,
			)
		);
		$Queries[] = "SET @regstr_uid:=(SELECT ID FROM _users WHERE Username=:un)";
		$strSQLPart_EmailID = T\DB::GetNewID_Combined(
						'_user_emails'
						, 'CombinedID'
						, 'UID=@regstr_uid'
						, NULL
						, array(
					'PrefixQuery' => "CONCAT(@regstr_uid, '_')"
						)
		);
		$Queries[] = array("INSERT INTO `_user_emails`(`CombinedID`, `UID`, `PendingEmail`)"
			. " VALUES(($strSQLPart_EmailID), @regstr_uid, :email)",
			array(':email' => $this->txtEmail)
		);
		$Queries[] = array("INSERT INTO `_user_recoveries`(`UID`, `Code`, `TimeStamp`, `PendingEmail`, `Type`, `CompanyDomain`)"
			. " VALUES(@regstr_uid, :code, :time, :email, :activation, :domain)",
			array(
				':code' => $this->_ActivationCode,
				':time' => time(),
				':email' => $this->txtEmail,
				':activation' => C\User::Recovery_Activation,
				//to detect company account recovery records we must join _users to _user_recoveries so there is no need to check CompanyDomain column
				':domain' => $IsCompany && !$this->_drCompanyInfo ? $this->_CompanyDomain : null,
			)
		);
		if ($IsCompany) {
			if ($this->txtCompanyURL) {
				if (!$this->_drCompanyInfo) {
					$Queries[] = array(
						"INSERT INTO `_company_info`(`OwnerUID`, `URL`) VALUES(@regstr_uid, :url)"
						, array(
							':url' => $this->txtCompanyURL
						)
					);
				} else {
					$Queries[] = array(
						"UPDATE `_company_info` SET `OwnerUID`=@regstr_uid, `URL`=:url"
						. " WHERE `ID`=:compid"
						, array(
							':url' => $this->txtCompanyURL,
							':compid' => $this->_drCompanyInfo['ID']
						)
					);
				}
			}
			//locations
			$Queries[] = array(
				"CALL geo_getGeoLocationIDs(
					:country
					, :division
					, :city
					, @regstr_CountryISO2
					, @regstr_DivisionCombined
					, @regstr_DivisionCode
					, @regstr_CityID);
				CALL geo_getUserLocationIDs(
					:country
					, :division
					, :city
					, @regstr_CountryISO2
					, @regstr_DivisionCombined
					, @regstr_CityID
					, @regstr_UserCountryID
					, @regstr_UserDivisionID
					, @regstr_UserCityID)"
				, array(
					':country' => $this->txtCountry? : $this->ddlCountry,
					':division' => $this->txtDivision? : $this->ddlDivision,
					':city' => $this->txtCity? : $this->ddlCity,
				)
			);
			$strSQLPart_LocationID = T\DB::GetNewID_Combined(
							'_user_locations'
							, 'CombinedID'
							, 'UID=@regstr_uid'
							, null
							, array('PrefixQuery' => "CONCAT(@regstr_uid, '_')"));
			$Queries[] = array(
				"INSERT INTO `_user_locations`(
					`CombinedID`
					, `UID`
					, `GeoCountryISO2`
					, `GeoDivisionCode`
					, `GeoCityID`
					, `UserCountryID`
					, `UserDivisionID`
					, `UserCityID`
					, `IsCurrentLocation`)
				VALUES(
					($strSQLPart_LocationID)
					, @regstr_uid
					, @regstr_CountryISO2
					, @regstr_DivisionCombined
					, @regstr_CityID
					, @regstr_UserCountryID
					, @regstr_UserDivisionID
					, @regstr_UserCityID
					, 1)"
			);
		}
		$Result = T\DB::Transaction($Queries, $CommonParams, function(\Exception $ex) {
					\html::ErrMsg_Exit(\t2::Site_User('Registration failed!'));
				});
		return $Result ? true : false;
	}

}
