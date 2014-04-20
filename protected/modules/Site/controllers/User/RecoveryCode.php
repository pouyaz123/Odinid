<?php

namespace Site\controllers\User;

use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class RecoveryCode extends \CAction {

	/**
	 * @param str $code Recovery code from $_GET
	 */
	public function run($code = null) {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Reset password'));
		$Model = new \Site\models\User\Recovery('Recovery');
		if ($code)
			$Model->attributes = array(
				'txtRecoveryCode' => $code,
			);
		if ($btn = \GPCS::POST('btnRecover'))
			$Model->attributes = \GPCS::POST('Recovery');
		#
		if ($btn && $Model->Recover()) {
			\Output::Render($this->controller, '../messages/success', array('msg' => \t2::site_site('Your new password has been set successfully.')));
		} else
			\Output::Render($this->controller, 'recoverycode'
					, array(
				'Model' => $Model,
					)
			);
	}

}
