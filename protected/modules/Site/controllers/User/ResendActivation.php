<?php

namespace Site\controllers\User;

use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class ResendActivation extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_user', 'Resend Activation Link');
		$Model = new \Site\models\User\Activation('ResendActivation');
		if ($btn = \GPCS::POST('btnResendActivationLink'))
			$Model->attributes = \GPCS::POST('ResendActivation');
		#
		if ($btn && $Model->ResendActivationLink()) {
			\Output::Render($this->controller, '../messages/success', array('msg' => \t2::Site_User('Activation link has been sent')));
		} else
			\Output::Render($this->controller, 'resendactivation'
					, array(
				'Model' => $Model,
					)
			);
	}

}
