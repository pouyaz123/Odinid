<?php

return array_merge(
		\Lng::GetCommonLangResourceArray('en/tr_user')
		, array(
//	'Account type' => 'Account type',
//	'Email' => 'Email',
//	'Confirm email' => 'Confirm email',
//	'Invitation code' => 'Invitation code',
	'Invalid invitation code' => 'Invalid invitation code. This code may be expired.',
//	'Register' => 'Register',
	'Registration failed!' => 'Registration failed! Please try again later.',
	'Registered successfully' => 'Registration has been done successfully. An activation link has been sent to your mailbox',
	#
	#activation link email template
//	'Activation link' => 'Activation link',
//	'User account has been activated.' => 'User account has been activated.',
//	'Activate' => 'Activate',
	#
//	'Activation code' => 'Activation code',
	'Invalid activation code' => 'Invalid activation code. This activation code(link) may be expired.',
	'Activation failed!' => 'Activation failed! Please try again later.',
		));
