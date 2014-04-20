<?php

namespace Site\controllers\User;

use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Activation extends \CAction {

	/**
	 * @param str $code activation code from $_GET
	 */
	public function run($code = null) {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Activation'));
		$Model = new \Site\models\User\Activation('Activation');
		if ($code)
			$Model->attributes = array(
				'txtActivationCode' => $code,
			);
		if ($btn = \GPCS::POST('btnActivate'))
			$Model->attributes = \GPCS::POST('Activation');
		#
		if (($btn || $code) && $Model->Activate()) {
			\Output::Render($this->controller, '../messages/success', array('msg' => \t2::site_site('User account has been activated.')));
		} else
			\Output::Render($this->controller, 'activation'
					, array(
				'Model' => $Model,
					)
			);
	}

}
