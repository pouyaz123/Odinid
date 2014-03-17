<?php

namespace Tools;

use \Tools as T;
use \Consts as C;

/**
 * Garbage Collection Cleaner (GCC)
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid cg network
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class GCC {

	//86400 seconds = 24hours
	const UserRecoveriesGCCPeriod = 86400;

	/**
	 * cleans expired user recoveries/activations periodically
	 * mytodo 1: multithread execution for gcc to have parallel processes
	 */
	static function UserRecoveries() {
		if (!T\Cache::rabbitCache()->get('GCC_UserRecoveries')) {
			ignore_user_abort(true);
//			T\DB::Execute("DELETE FROM `_user_recoveries`"
//					. " WHERE `TimeStamp`<:time AND `Type`!=:activation" //to avoid wild user accounts(like the c++ wild pointers)
//					, array(
//				':time' => time() - (T\Settings::GetValue('ActivationLink_LifeTime') * 60 * 60),
//				':activation' => C\User::Recovery_Activation,
//					)
//			);
			//mytodo 1: GCC : del long time non-activated users in _users and activations in _user_recoveries (use mysql procedure). Illegitimate occupied emails may prevent legitimate people
			T\Cache::rabbitCache()->set('GCC_UserRecoveries', 1, self::UserRecoveriesGCCPeriod);
			ignore_user_abort(false);
			if (connection_status() != CONNECTION_NORMAL)
				exit;
		}
	}

}

?>
