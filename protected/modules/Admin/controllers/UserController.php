<?php

namespace Admin\controllers;

use \Consts as C;
use \Tools as T;

class UserController extends \Admin\Components\BaseController {

	public $defaultAction = 'List';

	public function filters() {
		return array(
			array(
				'\Admin\filters\AdminAuthFilter',
			)
		);
	}

	public function actions() {
		return array(
			'Invitations' => '\Admin\controllers\User\InvitationsAction',
			'Permissions' => '\Admin\controllers\User\PermissionsAction',
			'Plans' => '\Admin\controllers\User\PlansAction',
			'Types' => '\Admin\controllers\User\TypesAction',
			'Users' => '\Admin\controllers\User\UsersAction',
		);
	}

}