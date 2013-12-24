<?php

namespace Admin\filters;

/**
 * Description of AdminAuthentication
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class AdminAuthFilter extends \CFilter {

	protected function preFilter($filterChain) {
		/* @var $filterChain \CFilterChain */
		if (!\Admin\models\AdminLogin::IsLoggedIn()) {
			\Tools\HTTP::Redirect_Immediately(\Admin\Consts\Routes::Login);
			return FALSE;
		} else {//mytodo 3: check the admin permission authority for the action here
			return true;
		}
	}

}
