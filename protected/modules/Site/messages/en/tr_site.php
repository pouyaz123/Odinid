<?php
return array_merge(
		\t2::GetCommonLangResourceArray('en/tr_common')
		, array(
//	'Sign in' => 'Sign in',
//	'Sign up' => 'Sign up',
#register
//	'Account type' => 'Account type',
//	'Email' => 'Email',
//	'Pending email' => 'Pending email',
//	'Confirm email' => 'Confirm email',
//	'Invitation code' => 'Invitation code',
	'Invalid invitation code' => 'Invalid invitation code. This code may be expired.',
//	'Register' => 'Register',
//	'Register Artist' => 'Register',
//	'Register Company' => 'Register',
	'Registration failed!' => 'Registration failed! Please try again later.',
	'Registered successfully' => 'Registration has been done successfully.',
	'Activation link sent successfully' => 'An activation link has been sent to your mailbox.',
#activation
//	'Activation' => 'Activation',	//page title
//	'Activate' => 'Activate',	//submit button
//	'Resend Activation Link' => 'Resend Activation Link',	//page title
//	'Resend' => 'Resend',	//submit button
//	'Activation link' => 'Activation link',	//email subject
//	'Activation code' => 'Activation code',
	'Invalid activation code' => 'Invalid activation code. This activation code(link) may be expired. '
	. ' <a href="'
	. \Yii::app()->createAbsoluteUrl(\Site\Consts\Routes::UserResendActivation)
	. '">Resend Activation Link</a>',
	'Activation failed!' => 'Activation failed! Please try again later.',
	'Failed to send activation link!' => 'Failed to send activation link email !',
	'Failed to send activation link! Resend' => 'Failed to send activation link email ! Please try'
	. ' <a href="'
	. \Yii::app()->createAbsoluteUrl(\Site\Consts\Routes::UserResendActivation)
	. '">Resend Activation Link</a>',
	'Activation link has been sent' => 'New activation link has been sent to your email.<br/> The link(code) has a short expiration time.',
	'User account has been activated.' => 'The email address and/or user account has been activated.',
#
	'Plz activate your account first' => 'Please activate your account first',
#recovery
	'Recovery' => 'Forget password? Recover', //page title
	'Reset password' => 'Reset password', //page title
//	'Recovery code' => 'Recovery code',	//form field
//	'Invalid recovery code' => 'Invalid recovery code',
//	'Recovery failed!' => 'Recovery failed!',
//	'Your new password has been set successfully.' => 'Your new password has been set successfully.',
//	'Recovery link' => 'Recovery link',	//email subject
//	'Failed to send recovery link!' => 'Failed to send recovery link!',
	'Recovery link has been sent' => 'Recovery link has been sent to your email.<br/> The link(code) has a short expiration time.',
//	'Recover' => 'Recover'
#change password
//	'Current username' => 'Current username',
	'Username changes' => 'Username changes : {0} of {1}',
//	'Current password' => 'Current password',
//	'New password' => 'New password',
//	'Confirm new password' => 'Confirm new password',
//	'Invalid password' => 'Invalid password',
#user info
//	'txtBirthday' => \t2::Site_User('Birthday'),
//	'ddlBirthdayFormat' => \t2::Site_User('Birthday view'),
//	'txtObjective' => \t2::Site_User('Objective'),
#user contact book
//	'txtPhone' => \t2::Site_User('Phone'),
//	'Phone Type' => 'Phone Type',
//	'txtEmail' => \t2::Site_User('Email'),
//	'txtWebAddress' => \t2::Site_Company('Web address'),
//	'txtWebAddressType' => \t2::Site_Company('Web address type'),
//	'This geographical location has been used previously'
#about
//	'About me'
	'User location' => '{City}, {Division}, {Country}',
#
//	'chkHealthInsurance' => \t2::Site_User('Health insurance'),
//	'chkOvertimePay' => \t2::Site_User('Overtime pay'),
//	'chkRetirementAccount' => \t2::Site_User('Retirement account'),
//	'ddlLevel' => \t2::Site_User('Level'),
//	'ddlEmploymentType' => \t2::Site_User('Employment type'),
//	'ddlSalaryType' => \t2::General('Salary type'),
//	'ddlWorkCondition' => \t2::Site_User('Work condition'),
//	'txtCompanyTitle' => \t2::Site_User('Company title'),
//	'txtJobTitle' => \t2::Site_User('Job title'),
//	'txtSalaryAmount' => \t2::Site_User('Salary amount'),
//	'txtTBALayoff' => \t2::Site_User('Layoff days (TBA)'),
//	'txtRetirementPercent' => \t2::Site_User('Retirement percent'),
#
//	'Study field' => 'Study field',
//	'Degree' => 'Degree',
//	'Update' => 'Update',
//	'Upload' => 'Upload',
//	'Crop' => 'Crop',
//	'Delete' => 'Delete',
//	'Basic info' => 'Basic info',
//	'Avatar' => 'Avatar',
//	'Availability' => 'Availability',
	'Setting' => 'Account setting',
//	'Phones' => 'Phones',
//	'Emails' => 'Emails',
//	'Private'=>'Private',
//	'Locations' => 'Locations',
//	'Work permissions' => 'Work permissions',
//	'Web addresses' => 'Web addresses',
//	'Experiences' => 'Experiences',
//	'Certificates' => 'Certificates',
//	'Educations' => 'Educations',
//	'Awards' => 'Awards',
//	'Additionals' => 'Additionals',
//	'Work fields' => 'Work fields',
//	'Skills' => 'Skills',
//	'Softwares' => 'Softwares',
//	'Languages' => 'Languages',
//	'Rate' => 'Rate',
	'TagsHelp' => 'Separate by commas(,)',
//	'Description' => 'Description',
//	'Date' => 'Date',
//	'Year' => 'Year',
//	'From date' => 'From date',
//	'To date' => 'To date',
//	'To present' => 'To present',
//	
//	'Country' => 'Country',
	'Division' => 'State | Province | Territory',
//	'City' => 'City',
//	'Address 1' => 'Address 1',
//	'Address 2' => 'Address 2',
//	'txtPostalCode' => \t2::Site_User('Postal code'),
//	'chkIsCurrentLocation' => \t2::Site_User('Is current location'),
//	'chkIsBillingLocation' => \t2::Site_User('Is billing location'),
//	'txtVisaType' => \t2::Site_User('Visa Type'),
//	
//	'Add new' => 'Add new',
//	'Set as primary' => 'Set as primary',
//	'Primary' => 'Primary',
	'Company has been claimed previously' => 'This company with this domain has been claimed previously!',
//	'The url's domain doesn't match to your email domain' => 'The url's domain doesn't match to your email domain'
//	'Web URL' => 'Web URL'
//	'ddlOperatingStatus' => \t2::Site_Company('Operating Status'),
//	'ddlHowManyStaffs' => \t2::Site_Company('How Many Staffs'),
//	'txtContactFirstName' => \t2::Site_Company('Contact First Name'),
//	'txtContactLastName' => \t2::Site_Company('Contact Last Name'),
//	'txtContactJobTitle' => \t2::Site_Company('Contact Job Title'),
//	'Rates' => 'Rates,
#
//	'Requested profile was not found' => 'Requested profile was not found',
//	'Gender' => 'Gender',
//	'Female' => 'Female',
//	'Man' => 'Man',
//	'First name' => 'First name',
//	'txtLastName' => 'Last name',
//	'Mid name' => 'Mid name',
//	'Talent search visibility' => 'Talent search visibility',
//	'Relocate internally' => 'Relocate internally',
//	'Relocate externally' => 'Relocate externally',
//	'Hire availability date' => 'Hire availability date',
//	'Hire availability type' => 'Hire availability type',
//	'Full' => 'Full',
//	'Part' => 'Part',
//	'Intern' => 'Intern',
//	'Contract' => 'Contract',
//	'Home' => 'Home',
#
//	'This combination has been taken previously' => 'This combination has been taken previously'
		)
);
