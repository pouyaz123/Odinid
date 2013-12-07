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
				'testLimit' => 2,
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
					, \Lng::Admin('tr_Common', "Logged in successfully"));

		$this->pageTitle = \Lng::AdminPageTitle('tr_Common', 'Login');

		$LoginForm = new \Admin\models\AdminLogin('Login');
		if ($LoginPost = \GPCS::POST('Login')) {
			$LoginForm->attributes = $LoginPost;
			if ($LoginForm->Login()) {
				T\HTTP::Redirect_Immediately(
						$this->createUrl(\Conf::AdminHomeRoute)
						, \Lng::Admin('tr_Common', "Logged in successfully"));
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
		$this->pageTitle = \Lng::AdminPageTitle('tr_Common', 'Cartable');
		$this->SetInternalEnv();
		\html::PushStateScript();
		$LastLoginTimeStamp = \Admin\models\AdminLogin::GetAdminSessionDR('LastLoginTimeStamp');
		if ($LastLoginTimeStamp)
			$LastLoginTimeStamp = gmdate('r', $LastLoginTimeStamp);
		\Output::Render($this, 'cartable', array(
			'LastLoginTime' => $LastLoginTimeStamp,
			'LastLoginIP' => \Admin\models\AdminLogin::GetAdminSessionDR('LastLoginIP'),
		));
	}

}