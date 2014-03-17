<?php

namespace Site\filters;

use Site\models\User\Login;
use Tools as T;

/**
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class UserAuth extends \CFilter {

	/**
	 * @param \CFilterChain $filterChain
	 * @return boolean
	 */
	protected function preFilter($filterChain) {
		if (!Login::IsLoggedIn()) {
			\Tools\HTTP::Redirect_Immediately(\Site\Consts\Routes::UserLogin);
			return FALSE;
		} else {//mytodo 2: check the user permission authority for the action here
			return true;
		}
	}

}
