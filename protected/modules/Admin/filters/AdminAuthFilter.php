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
			\Tools\HTTP::Redirect_Immediately($filterChain->controller->createUrl(\Admin\Consts\Routes::Login));
			return FALSE;
		} else {//TODO1: check the admin permission authority for the action here
			return true;
		}
	}

}

?>
