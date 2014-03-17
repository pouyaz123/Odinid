<?php

namespace Site\controllers;

class defaultController extends \Site\Components\BaseController {

	public function actionDefault() {
		$this->render('default');
	}

}