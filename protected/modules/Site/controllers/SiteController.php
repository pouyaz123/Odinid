<?php

namespace Site\controllers;

class SiteController extends \Site\Components\BaseController {

	/**
	 * 
	 */
	public function actionDefault() {
		$this->render('default');
	}

}