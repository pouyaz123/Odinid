<?php

namespace Admin\controllers\User;

use \Consts as C;
use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Users extends \CAction {

	public function run() {
		$ctrl = $this->controller;
		/* @var $ctrl \Admin\controllers\UserController */
		$ctrl->pageTitle = \t2::AdminPageTitle('User list');

//		$dg = $this->DataGrid($ctrl);

		\Output::Render($ctrl, 'users'/*, array('dg' => $dg)*/);
	}

	private function DataGrid(\Admin\controllers\UserController $ctrl) {
	}

}
