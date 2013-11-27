<?php

namespace Admin\controllers\User;

use Consts as C;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class InvitationsAction extends \CAction {

	public function run() {
		$ctrl = $this->controller;
		/* @var $ctrl \Admin\controllers\UserController */
		$ctrl->pageTitle = \Lng::AdminPageTitle('Modules', 'Invitations');

//		$dg = $this->DataGrid($ctrl);

		$ctrl->SetInternalEnv();
		\html::PushStateScript();
		\Output::Render($ctrl, 'invitations'/*, array('dg' => $dg)*/);
	}

	private function DataGrid(\Admin\controllers\UserController $ctrl) {
	}

}

?>
