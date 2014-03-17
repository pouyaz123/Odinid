<?php

namespace Site\Consts;

final class Routes {

	const Home = '/';

	#---------- user basics
	const UserRegister = '/user/register';
	const UserLogin = '/user/login';
	const UserLogout = '/user/logout';

	/** activates based on the code */
	const UserActivation = '/user/activation';

	/** sends activation emails */
	const UserResendActivation = '/user/resendactivation';

	/** sends recovery emails */
	const UserRecovery = '/user/recovery';

	/** recovers based on the code */
	const UserRecoveryCode = '/user/recoverycode';

	#---------- user info
	const UserProfile = "/me";

	private static function GetUsername($Username = null) {
		if (!$Username)
			$Username = \Site\models\User\Login::GetSessionDR('Username');
		return $Username;
	}

	static function UserProfile($Username = null) {
		return "/" . self::GetUsername($Username);
	}

	static function UserEditInfo($Username = null) {
		return "/" . self::GetUsername($Username) . "/editinfo";
	}

	static function UserEditContacts($Username = null) {
		return "/" . self::GetUsername($Username) . "/editcontacts";
	}

}
