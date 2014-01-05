<?php

namespace Site\controllers;

class UserController extends \Site\Components\BaseController {

	//layout "column1" has been chosen by BaseContoller
	public function actions() {
		return array(
			'register' => '\Site\controllers\User\Register',
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'testLimit' => 2,
				'minLength' => 3,
				'maxLength' => 5,
			),
			'activation' => '\Site\controllers\User\Activation',
			'activation_resend' => '\Site\controllers\User\ResendActivation',
//			'resendactcode' => '\Site\controllers\User\ResendActCodeAction',
//			'login' => '\Site\controllers\User\LoginAction',
//			'recovery' => '\Site\controllers\User\RecoveryAction',
//			'settings' => '\Site\controllers\User\SettingsAction',
//			'info' => '\Site\controllers\User\InfoAction',
//			'profile' => '\Site\controllers\User\ProfileAction',
		);
	}

}
