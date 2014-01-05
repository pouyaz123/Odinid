<?php

namespace Site\models\User;

use \Consts as C;
use \Tools as T;

/**
 * @property-read string $ActivationCode the activation code after a registration process
 * @method boolean Register() user registration //triggers the relater event handlers
 */
class Register extends \Base\FormModel {

	const UserType_Company = 'Company';
	const UserType_Artist = 'Artist';

	public function getPostName() {
		return 'Register';
	}

	//attrs
	public $ddlAccountType = self::UserType_Artist;
	public $txtEmail;
	public $txtEmailRepeat;
	public $txtUsername;
	public $txtPassword;
	public $txtCaptcha;
	#artist
	public $txtInvitationCode;
	#company
	public $txtCompanyURL = false;
	#geolocations
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
	private $_drUserType = NULL;
	private $_arrAccountTypes = array(
		self::UserType_Artist => 'Artist',
		self::UserType_Company => 'Company',
	);

	public function getArrAccountTypes() {
		return $this->_arrAccountTypes;
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
		return array(
			#common
			array('txtEmail, txtUsername, txtPassword, txtCaptcha', 'required'),
			array('ddlAccountType', 'in', 'range' => array_keys($this->_arrAccountTypes)),
			array('txtPassword', 'length',
				'min' => C\Regexp::Password_MinLength),
			array('txtCaptcha', 'MyCaptcha'),
			#email
			array('txtEmail', 'email'),
			array('txtEmailRepeat', 'compare',
				'compareAttribute' => 'txtEmail'),
			array('txtEmail', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_users` u'
				. ' INNER JOIN (SELECT 1) tmp ON u.`PrimaryEmail`=:val'
				. ' LEFT JOIN `_user_recoveries` ur ON ur.`PendingEmail`=:val'
				. ' LIMIT 1'),
			#username
			array('txtUsername', 'match', 'pattern' => C\Regexp::Username),
			array('txtUsername', 'match', 'not' => true, 'pattern' => C\Regexp::Username_InvalidCases),
			array('txtUsername', 'length',
				'min' => C\Regexp::Username_MinLen, 'max' => C\Regexp::Username_MaxLen),
			array('txtUsername', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_users` WHERE `Username`=:val LIMIT 1'),
			#artist
			array('txtInvitationCode', 'required',
				'on' => 'ArtistRegister'),
			array('txtInvitationCode', 'IsValidInvitation',
				'on' => 'ArtistRegister'),
			#company
			array('ddlCountry, ddlDivision, ddlCity, txtCountry, txtDivision, txtCity', 'required', //, txtAddress1, txtAddress2
				'on' => 'CompanyRegister'),
			array('txtCompanyURL', 'url',
				'on' => 'CompanyRegister'),
			array('txtCompanyURL', 'IsClaimedCompanyDomain',
				'on' => 'CompanyRegister'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'ddlAccountType' => \Lng::Site('tr_user', 'Account type'),
			'txtEmail' => \Lng::Site('tr_user', 'Email'),
			'txtEmailRepeat' => \Lng::Site('tr_user', 'Confirm email'),
			'txtUsername' => \Lng::Site('tr_user', 'Username'),
			'txtPassword' => \Lng::Site('tr_user', 'Password'),
			'txtCaptcha' => \Lng::General('Captcha code'),
			#artist
			'txtInvitationCode' => \Lng::Site('tr_user', 'Invitation code'),
			#Company
			'txtCompanyURL' => \Lng::Site('tr_company', 'Company web URL'),
			#location
			'ddlCountry' => \Lng::Site('tr_common', 'Country'),
			'ddlDivision' => \Lng::Site('tr_common', 'Division'),
			'ddlCity' => \Lng::Site('tr_common', 'City'),
			'txtCountry' => \Lng::Site('tr_common', 'Country'),
			'txtDivision' => \Lng::Site('tr_common', 'Division'),
			'txtCity' => \Lng::Site('tr_common', 'City'),
//			'txtAddress1' => \Lng::Site('tr_common', 'Address 1'),
//			'txtAddress2' => \Lng::Site('tr_common', 'Address 2'),
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
			$this->addError('txtInvitationCode', \Lng::Site('tr_user', 'Invalid invitation code'));
	}

	function IsClaimedCompanyDomain($attr, $params) {
		if ($this->txtCompanyURL && $this->txtEmail && preg_match(C\Regexp::Email, $this->txtEmail)) {
			$Domain = explode('@', $this->txtEmail);
			$Domain = $Domain[1];
			$URLDomain = parse_url($this->txtCompanyURL, PHP_URL_HOST);
			if (!preg_match(C\Regexp::CompanyURLDomain(preg_quote($Domain, '/')), $URLDomain))
				$this->addError($attr, Lng::Site('tr_company', "The url's domain doesn't match to your email domain"));
			else {
				$this->_CompanyDomain = $Domain;
				if (T\DB::GetField(
								"SELECT COUNT(*)"
								. " FROM `_company_info`"
								. " WHERE `Domain`=:domain AND NOT ISNULL(`OwnerUID`)"
								, array(':domain' => $Domain)))
					$this->addError($attr, \Lng::Site('tr_company', 'Company has been claimed previously'));
			}
		}
	}

	function onAfterRegister() {
		$this->CleanViewStateOfSpecialAttrs();
	}

	function callRegister() {
		if (!$this->validate())
			return false;

		$this->_ActivationCode = T\DB::GetUniqueCode('_user_recoveries', 'Code');
		$PrimaryUserID = Login::GetSessionDR('ID');
		$IsCompany = ($this->ddlAccountType == self::UserType_Company);
		$drUserType = &$this->_drUserType;
		$strSQLPart_UserType = $drUserType ?
				":utid" :
				"SELECT `ID` FROM `_user_types` WHERE `IsDefault`";
		$Queries = array();
		$CommonParams = array(
			':un' => $this->txtUsername,
		);
		$Queries[] = array("INSERT INTO `_users`(`ParentAccountID`, `AccountType`, `UserTypeID`, `UTypeExpDate`, `Username`, `Password`, `RegisterDateTime`)"
			. " VALUES(:parentid, :accounttype, ($strSQLPart_UserType), :utexp, :un, :pw, :registertime)",
			array(
				':parentid' => $PrimaryUserID,
				':accounttype' => $this->ddlAccountType,
				':utid' => $drUserType ? $drUserType['UserTypeID'] : NULL,
				':utexp' => $drUserType ? $drUserType['UserTypeExpDate'] : NULL,
				':pw' => $this->txtPassword,
				':registertime' => gmdate('Y-m-d H:i:s'),
			)
		);
		$Queries[] = array("INSERT INTO `_user_recoveries`(`UID`, `Code`, `TimeStamp`, `PendingEmail`, `Type`, `CompanyDomain`)"
			. " VALUES(@regstr_uid:=(SELECT ID FROM _users WHERE Username=:un), :code, :time, :email, :activation, :domain)",
			array(
				':code' => $this->_ActivationCode,
				':time' => time(),
				':email' => $this->txtEmail,
				':activation' => C\User::Recovery_Activation,
				':domain' => $IsCompany ? $this->_CompanyDomain : null,
			)
		);
		if ($IsCompany) {
			//company url
			$Queries[] = array(
				"INSERT INTO `_company_info`(`OwnerUID`, `URL`)"
				. " VALUES(@regstr_uid, :url)"
				, array(
					':url' => $this->txtCompanyURL
				)
			);
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
					':city_quoted' => preg_quote($this->txtCity? : $this->ddlCity),
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
						, `UserCityID`)
					VALUES(
						($strSQLPart_LocationID)
						, @regstr_uid
						, @regstr_CountryISO2
						, @regstr_DivisionCombined
						, @regstr_CityID
						, @regstr_UserCountryID
						, @regstr_UserDivisionID
						, @regstr_UserCityID
					)"
			);
		}
		$Result = T\DB::Transaction($Queries, $CommonParams, function(\Exception $ex) {
					\html::ErrMsg_Exit(\Lng::Site('tr_user', 'Registration failed!'));
				});
		return $Result ? true : false;
	}

}
