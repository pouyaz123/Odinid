<?php

namespace Site\controllers;

use \Consts as C;
use \Tools as T;

class UserController extends \Site\Components\BaseController {

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
			'resendactivation' => '\Site\controllers\User\ResendActivation',
			'recovery' => '\Site\controllers\User\Recovery',
			'recoverycode' => '\Site\controllers\User\RecoveryCode',
		);
	}

	public function actionLogin() {
		if (\Site\models\User\Login::IsLoggedIn())
			T\HTTP::Redirect_Immediately(
					$this->createAbsoluteUrl(\Conf::UserHomeRoute())
					, \t2::Site_User("Logged in successfully"));

		$this->pageTitle = \t2::SitePageTitle('tr_common', 'Login');

		$Model = new \Site\models\User\Login('Login');
		$Post = \GPCS::POST('Login');
		if (\GPCS::POST('btnLogin') && $Post) {
			$Model->attributes = $Post;
			if ($Model->Login()) {
				T\HTTP::Redirect_Immediately(
						$this->createAbsoluteUrl(\Conf::UserHomeRoute())
						, \t2::Site_User("Logged in successfully"));
			}
		}
		\Output::Render($this, 'login', array('Model' => $Model), 'formLogin');
	}

	public function actionLogout() {
		\Site\models\User\Login::Logout();
		T\HTTP::Redirect_Immediately($this->createUrl(\Site\Consts\Routes::UserLogin));
	}

}
