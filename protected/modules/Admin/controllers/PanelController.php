<?php

namespace Admin\controllers;

use \Consts as C;
use \Tools as T;

class PanelController extends \Admin\Components\BaseController {

	public $defaultAction = 'Cartable';

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'testLimit' => 1,
				'minLength' => 3,
				'maxLength' => 4,
			)
		);
//		new \CCaptchaAction;
	}

	public function filters() {
		return array(
			array(
				'\Admin\filters\AdminAuthFilter - Login, Logout, captcha',
			)
		);
	}

	public function actionLogin() {
		if (\Admin\models\AdminLogin::IsLoggedIn())
			T\HTTP::Redirect_Immediately(
					$this->createUrl(\Conf::AdminHomeRoute)
					, \Lng::Admin('User', "Welcome, Admin panel is loading ..."));

		$this->pageTitle = \Lng::AdminPageTitle('User', 'Login');

		$LoginForm = new \Admin\models\AdminLogin('Login');
		if ($LoginPost = \GPCS::POST('Login')) {
			$LoginForm->attributes = $LoginPost;
			if ($LoginForm->Login()) {
				T\HTTP::Redirect_Immediately(
						$this->createUrl(\Conf::AdminHomeRoute)
						, \Lng::Admin('User', "Welcome, Admin panel is loading ..."));
			}
		}
		$this->layout = 'outerpages';
		\Output::Render($this, 'login', array('Model' => $LoginForm), 'formLogin');
	}

	public function actionLogout() {
		\Admin\models\AdminLogin::Logout();
		T\HTTP::Redirect_Immediately($this->createUrl(\Admin\Consts\Routes::Login));
	}

	public function actionCartable() {
		$this->pageTitle = \Lng::AdminPageTitle('User', 'Cartable');
		$this->SetInternalEnv();
		\html::PushStateScript();
		\Output::Render($this, 'cartable');
	}

}