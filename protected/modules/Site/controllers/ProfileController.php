<?php

namespace Site\controllers;

use Site\models\User\Login;
use Tools as T;

class ProfileController extends \Site\Components\BaseController {

	public $defaultAction = 'about';

	public function init() {
		parent::init();
		if (\GPCS::GET('username') == 'me') {
			if (!Login::IsLoggedIn())
				T\HTTP::Redirect_Immediately(\Site\Consts\Routes::UserLogin);
			T\HTTP::Redirect_Immediately(
					\Site\Consts\Routes::User_Profile()
			);
		}
	}

	//layout "column1" has been chosen by BaseContoller
	public function actions() {
		return array(
//			'captcha' => array(
//				'class' => 'CCaptchaAction',
//				'testLimit' => 2,
//				'minLength' => 3,
//				'maxLength' => 5,
//			),
			'about' => '\Site\controllers\Profile\About',
			'editinfo' => '\Site\controllers\Profile\EditInfo',
			'editcontacts' => '\Site\controllers\Profile\EditContacts',
			'editemails' => '\Site\controllers\Profile\EditEmails',
			'editlocations' => '\Site\controllers\Profile\EditLocations',
			'editresidencies' => '\Site\controllers\Profile\EditResidencies',
			'settings' => '\Site\controllers\Profile\Settings',
		);
	}

	public function filters() {
		return array(
			array(
				'\Site\filters\UserAuth - about',
			)
		);
	}

}
