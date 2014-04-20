<?php

namespace Site\controllers\User;

use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Recovery extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Recovery'));
		$Model = new \Site\models\User\Recovery('SendRecoveryLink');
		if ($btn = \GPCS::POST('btnSendRecoveryLink'))
			$Model->attributes = \GPCS::POST('SendRecoveryLink');
		#
		if ($btn && $Model->SendRecoveryLink()) {
			\Output::Render($this->controller, '../messages/success', array('msg' => \t2::site_site('Recovery link has been sent')));
		} else
			\Output::Render($this->controller, 'recovery'
					, array(
				'Model' => $Model,
					)
			);
	}

}
