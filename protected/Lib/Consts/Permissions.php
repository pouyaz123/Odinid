<?php

namespace Consts;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 1
 * @access public
 */
final class Permissions {
	#----------------- PERMISSIONS -----------------#

	const Accessibility = 2
			, Add = 4
			, EditOrDelete = 8;
	const All = 14;
//
	const PrivateAccess = 1;
	const FAccess = 2;
	const FoFAccess = 4;
	const PublicAccess = 8;
//
	const AcceptedConnection = 1;
	const BlockedConnection = 2;

	/* Quick Guide
	 * 1 = 0001
	 * 2 = 0010
	 * 4 = 0100
	 * 8 = 1000
	 * 8 | 2 = 1010 = 10
	 * 8 & 10 = 1000 = 8
	 * 4 & 10 = 0000 = 0 */
}

