<?php

namespace Site\controllers;

use Site\models\User\Login;
use Tools as T;

class ProjectsController extends \Site\Components\BaseController {

	public $defaultAction = 'about';

	//layout "column1" has been chosen by BaseContoller
	public function actions() {
		return array(
		);
	}

	public function filters() {
		return array(
			array(
				'\Site\filters\UserAuth',
			)
		);
	}

}
