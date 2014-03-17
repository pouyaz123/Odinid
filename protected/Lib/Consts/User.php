<?php

namespace Consts;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
final class User {

	//statuses
	const Status_Active = 'Active';
	const Status_Pending = 'Pending';
	const Status_Disabled = 'Disabled';
	const Status_Suspended = 'Suspended';
	//recovery types
	const Recovery_Activation = 'Activation';
	const Recovery_Recovery = 'Recovery';
	const Recovery_EmailVerify = 'EmailVerify';
	//account types
	const UserAccountType_Artist = 'Artist';
	const UserAccountType_Company = 'Company';

}
