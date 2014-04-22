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
			'editavatar' => '\Site\controllers\Profile\EditAvatar',
			'setting' => '\Site\controllers\Profile\Setting',
			#
			'editcontacts' => '\Site\controllers\Profile\EditContacts',
			'editemails' => '\Site\controllers\Profile\EditEmails',
			'editwebaddresses' => '\Site\controllers\Profile\EditWebAddresses',
			#
			'editlocations' => '\Site\controllers\Profile\EditLocations',
			'editresidencies' => '\Site\controllers\Profile\EditResidencies',
			#
			'editskills' => '\Site\controllers\Profile\EditSkills',
			'editsoftwares' => '\Site\controllers\Profile\EditSoftwares',
			'editlanguages' => '\Site\controllers\Profile\EditLanguages',
			'editworkfields' => '\Site\controllers\Profile\EditWorkFields',
			'editcertificates' => '\Site\controllers\Profile\EditCertificates',
			'editawards' => '\Site\controllers\Profile\EditAwards',
			'editexperiences' => '\Site\controllers\Profile\EditExperiences',
			'editadditionals' => '\Site\controllers\Profile\EditAdditionals',
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
