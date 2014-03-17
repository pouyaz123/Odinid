<?php

namespace Admin\controllers;

use \Consts as C;
use \Tools as T;

class UserController extends \Admin\Components\BaseController {

	public $defaultAction = 'Users';

	public function actions() {
		return array(
			'invitations' => '\Admin\controllers\User\Invitations',
			'permissions' => '\Admin\controllers\User\Permissions',
			'plans' => '\Admin\controllers\User\Plans',
			'types' => '\Admin\controllers\User\Types',
			'users' => '\Admin\controllers\User\Users',
		);
	}

}