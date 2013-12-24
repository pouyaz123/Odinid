<?php

namespace Site\controllers\User;

use Tools as T;

/**
 * Description of RegisterAction
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class RegisterAction extends \CAction {

	/**
	 * @param str $code invitation code from $_GET
	 */
	public function run($code = null, $type = null) {
		\CHtml::$afterRequiredLabel = '*';
		$userModel = new \Site\models\User\Register();
		$RegisterPost = \GPCS::POST('Register');
		#ini attrs
		$userModel->attributes = array(
			'txtInvitationCode' => $code,
			'ddlAccountType' => isset($RegisterPost['ddlAccountType']) ?
					$RegisterPost['ddlAccountType'] :
					($type ? ucfirst($type) : $userModel->ddlAccountType),
		);
		switch ($userModel->ddlAccountType) {
			case $userModel::UserType_Company:
				$userModel->scenario = 'CompanyRegister';
				#company geo location widget
				$wdgGeoLocation = $this->controller->createWidget(
						'\Widgets\GeoLocationFields\GeoLocationFields'
						, array(
					'id' => 'GeoDDLs',
					'Model' => $userModel,
					'ddlCountryAttr' => 'ddlCountry',
					'ddlDivisionAttr' => 'ddlDivision',
					'ddlCityAttr' => 'ddlCity',
					'txtCountryAttr' => 'txtCountry',
					'txtDivisionAttr' => 'txtDivision',
					'txtCityAttr' => 'txtCity',
					'PromptDDLOption' => 'select',
				));
				/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
				\html::PushStateScript('?type=company');
				break;
			case $userModel::UserType_Artist:
				$userModel->scenario = 'ArtistRegister';
				\html::PushStateScript('?type=artist');
				break;
		}
		#
		$RegisterResult = false;
		if (\GPCS::POST('btnRegister') && $RegisterPost) {
			$userModel->attributes = $RegisterPost;
			$RegisterResult = $userModel->Register($ActivationCode);
		} else
			\Base\FormModel::AjaxValidation('Register', $userModel, true);
		#
		if ($RegisterResult) {
			\Site\models\User\Activation::SendActivationEmail($ActivationCode, $userModel->txtEmail, $userModel->txtUsername);
			\Output::Render($this->controller, '../messages/success', array('msg' => \Lng::Site('User', 'Registered successfully')));
		} else
			\Output::Render($this->controller, 'register'
					, array(
				'Model' => $userModel,
				'wdgGeoLocation' => $userModel->ddlAccountType == $userModel::UserType_Company ? $wdgGeoLocation : null,
					)
			);
	}

}
