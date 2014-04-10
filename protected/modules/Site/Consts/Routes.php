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

	static function __callStatic($Name, $Params) {
		$Name = explode('_', $Name);
		$Username = isset($Params[0]) ? $Params[0] : null;
		if (isset($Name[1]))
			$Name[1] = strtolower($Name[1]);
		switch ($Name[0]) {
			case 'User':
				return "/" . self::GetUsername($Username) . ($Name[1] != 'profile' ? '/' . $Name[1] : '');
				break;
		}
	}

}
