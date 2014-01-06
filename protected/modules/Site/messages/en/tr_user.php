<?php
return array_merge(
		\t2::GetCommonLangResourceArray('en/tr_user')
		, array(
//	'Account type' => 'Account type',
//	'Email' => 'Email',
//	'Confirm email' => 'Confirm email',
//	'Invitation code' => 'Invitation code',
	'Invalid invitation code' => 'Invalid invitation code. This code may be expired.',
//	'Register' => 'Register',
//	'Register Artist' => 'Register',
//	'Register Company' => 'Register',
	'Registration failed!' => 'Registration failed! Please try again later.',
	'Registered successfully' => 'Registration has been done successfully. An activation link has been sent to your mailbox.',
	#
	#
//	'Activation' => 'Activation',	//page title
//	'Activate' => 'Activate',	//submit button
//	'Resend Activation Link' => 'Resend Activation Link',	//page title
//	'Resend' => 'Resend',	//submit button
//	'Activation link' => 'Activation link',	//email title
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
	'Activation link has been sent' => 'New activation link has been sent to your email.',
//	'User account has been activated.' => 'User account has been activated.',
		)
);
