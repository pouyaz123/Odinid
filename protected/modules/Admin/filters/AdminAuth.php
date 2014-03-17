<?php

namespace Admin\filters;

/**
 * Description of Admin Auth(enticate | orization)
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class AdminAuth extends \CFilter {

	/**
	 * @param \CFilterChain $filterChain
	 * @return boolean
	 */
	protected function preFilter($filterChain) {
		if (!\Admin\models\AdminLogin::IsLoggedIn()) {
			\Tools\HTTP::Redirect_Immediately(\Admin\Consts\Routes::Login);
			return FALSE;
		} else {//mytodo 3: check the admin permission authority for the action here
			return true;
		}
	}

}
