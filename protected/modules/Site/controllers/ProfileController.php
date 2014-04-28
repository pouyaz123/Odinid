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
			'about' => '\Site\controllers\EditProfile\About',
			'editinfo' => '\Site\controllers\EditProfile\Info',
			'editavatar' => '\Site\controllers\EditProfile\Avatar',
			'setting' => '\Site\controllers\EditProfile\Setting',
			#
			'editcontacts' => '\Site\controllers\EditProfile\Contacts',
			'editemails' => '\Site\controllers\EditProfile\Emails',
			'editwebaddresses' => '\Site\controllers\EditProfile\WebAddresses',
			#
			'editlocations' => '\Site\controllers\EditProfile\Locations',
			'editresidencies' => '\Site\controllers\EditProfile\Residencies',
			#
			'editskills' => '\Site\controllers\EditProfile\Skills',
			'edittools' => '\Site\controllers\EditProfile\Tools',
			'editlanguages' => '\Site\controllers\EditProfile\Languages',
			'editworkfields' => '\Site\controllers\EditProfile\WorkFields',
			'editcertificates' => '\Site\controllers\EditProfile\Certificates',
			'editeducations' => '\Site\controllers\EditProfile\Educations',
			'editawards' => '\Site\controllers\EditProfile\Awards',
			'editexperiences' => '\Site\controllers\EditProfile\Experiences',
			'editadditionals' => '\Site\controllers\EditProfile\Additionals',
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
