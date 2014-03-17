<?php
return array_merge(
		\t2::GetCommonLangResourceArray('en/tr_user')
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
	'Registered successfully' => 'Registration has been done successfully. An activation link has been sent to your mailbox.',
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
	'Failed to send activation link!' => 'Failed to send activation link email! Please try'
	. ' <a href="'
	. \Yii::app()->createAbsoluteUrl(\Site\Consts\Routes::UserResendActivation)
	. '">Resend Activation Link</a>',
	'Activation link has been sent' => 'New activation link has been sent to your email.<br/> The link(code) has a short expiration time.',
//	'User account has been activated.' => 'User account has been activated.',
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
//	'New password' => 'New password',
//	'Confirm new password' => 'Confirm new password',
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
//	'You reached the maximum number of contacts' => 'You reached the maximum number of contacts'
//	'You reached the maximum number of web addresses' => 'You reached the maximum number of web addresses'
//	'You reached the maximum number of locations' => 'You reached the maximum number of locations'
//	'You reached the maximum number of residencies' => 'You reached the maximum number of residencies'
//	'This geographical location has been used previously'
#about
//	'About me'
	'User location' => '{City}, {Division}, {Country}',
#
//	'Skills' => 'Skills',
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
	'Update' => 'Update',
	'Edit basic info' => 'Basic info',
	'Edit contact info' => 'Contacts',
		)
);
