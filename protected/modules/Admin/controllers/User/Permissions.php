<?php

namespace Admin\controllers\User;

use \Consts as C;
use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Permissions extends \CAction {

	public function run() {
		$ctrl = $this->controller;
		/* @var $ctrl \Admin\controllers\UserController */

//		$dg = $this->DataGrid($ctrl);

//		\html::PushStateScript();
		\Output::Render($ctrl, 'permissions'/*, array('dg' => $dg)*/);
	}

	private function DataGrid(\Admin\controllers\UserController $ctrl) {
	}

}
