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
	}

	public function filters() {
		return array(
			array(
				'\Admin\filters\AdminAuth - Login, Logout, captcha',
			)
		);
	}

	public function actionLogin() {
		if (\Admin\models\AdminLogin::IsLoggedIn())
			T\HTTP::Redirect_Immediately(
					$this->createAbsoluteUrl(\Conf::AdminHomeRoute)
					, \t2::Admin_User("Logged in successfully"));

		$this->pageTitle = \t2::AdminPageTitle('tr_common', 'Login');

		$Model = new \Admin\models\AdminLogin('Login');
		$Post = \GPCS::POST('Login');
		if (\GPCS::POST('btnLogin') && $Post) {
			$Model->attributes = $Post;
			if ($Model->Login()) {
				T\HTTP::Redirect_Immediately(
						$this->createAbsoluteUrl(\Conf::AdminHomeRoute)
						, \t2::Admin_User("Logged in successfully"));
			}
		}
		\Output::Render($this, 'login', array('Model' => $Model), 'formLogin');
	}

	public function actionLogout() {
		\Admin\models\AdminLogin::Logout();
		T\HTTP::Redirect_Immediately($this->createUrl(\Admin\Consts\Routes::Login));
	}

	public function actionCartable() {
		$this->pageTitle = \t2::AdminPageTitle('tr_common', 'Cartable');
		\html::PushStateScript();
		$LastLoginTStamp = \Admin\models\AdminLogin::GetSessionDR('LastLoginTStamp');
		if ($LastLoginTStamp)
			$LastLoginTStamp = gmdate('r', $LastLoginTStamp);
		\Output::Render($this, 'cartable', array(
			'LastLoginTime' => $LastLoginTStamp,
			'LastLoginIP' => \Admin\models\AdminLogin::GetSessionDR('LastLoginIP'),
		));
	}

}
