<?php

namespace Site\controllers\User;

use Tools as T;

/**
 * Description of ResendActivation
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class ResendActivation extends \CAction {

	/**
	 * @param str $code activation code from $_GET
	 */
	public function run() {
		$Model = new \Site\models\User\Activation('ResendActivation');
		$ThePost = \GPCS::POST('Activation');
		if (!$ThePost)
			$ThePost = \GPCS::POST('ResendActivation');
		#ini attrs
		$Model->attributes = array(
			'txtActivationCode' => $code,
		);
		switch ($Model->ddlAccountType) {
			case $Model::UserType_Company:
				$Model->scenario = 'CompanyRegister';
				break;
			case $Model::UserType_Artist:
				$Model->scenario = 'ArtistRegister';
				break;
		}
		#
		$RegisterResult = false;
		if (\GPCS::POST('btnRegister') && $ThePost) {
			$Model->attributes = $ThePost;
			$RegisterResult = $Model->Register();
			$ActivationCode = $Model->ActivationCode;
		} else
			\Base\FormModel::AjaxValidation('Register', $Model, true);
		#
//		if ($RegisterResult) {
//			\Site\models\User\Activation::SendActivationEmail(
//					$ActivationCode
//					, $userModel->txtEmail
//					, $userModel->txtUsername);
//			\Output::Render($this->controller, '../messages/success', array('msg' => \Lng::Site('tr_user', 'Registered successfully')));
//		} else
		\Output::Render($this->controller, 'register'
				, array(
			'Model' => $Model,
			'wdgGeoLocation' => $Model->ddlAccountType == $Model::UserType_Company ? $wdgGeoLocation : null,
				)
		);
	}

}
