<?php

namespace Site\controllers;

class UserController extends \Site\Components\BaseController {

	//layout "column1" has been chosen by BaseContoller
	public function actions() {
		return array(
//			'captcha' => '\Site\Components\CaptchaAction',
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'testLimit' => 1,
			)
//			'register'=>'\Site\controllers\User\RegisterAction',
//			'activate' => '\Site\controllers\User\ActivateAction',
//			'resendactcode' => '\Site\controllers\User\ResendActCodeAction',
//			'login' => '\Site\controllers\User\LoginAction',
//			'recovery' => '\Site\controllers\User\RecoveryAction',
//			'settings' => '\Site\controllers\User\SettingsAction',
//			'info' => '\Site\controllers\User\InfoAction',
//			'profile' => '\Site\controllers\User\ProfileAction',
		);
	}

	/**
	 * 
	 * @param str $code invitation code from $_GET
	 */
	public function actionRegister($code = null) {
		$user = new \Site\models\User('Register');
		if ($code)
			$user->txtInvitationCode = $code;
		self::AjaxValidation('Register', $user, true);
		$ResgiterPost = \GPCS::POST('Register');
		if ($ResgiterPost) {
			$user->attributes = $ResgiterPost;
			$user->Register();
		}
		$this->render('register', array('Model' => $user, 'InvitationCode' => $code));
	}

}