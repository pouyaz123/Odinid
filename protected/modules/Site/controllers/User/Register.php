<?php

namespace Site\controllers\User;

use Tools as T;

/**
 * Description of RegisterAction
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Register extends \CAction {

	/**
	 * @param str $code invitation code from $_GET
	 */
	public function run($code = null, $type = null) {
		$Model = new \Site\models\User\Register();
		$ThePost = \GPCS::POST('Register');
		#ini attrs
		$Model->attributes = array(
			'txtInvitationCode' => $code,
			'ddlAccountType' => isset($ThePost['ddlAccountType']) ?
					$ThePost['ddlAccountType'] :
					($type ? ucfirst($type) : $Model->ddlAccountType),
		);
		switch ($Model->ddlAccountType) {
			case $Model::UserType_Company:
				$Model->scenario = 'CompanyRegister';
				#company geo location widget
				$wdgGeoLocation = $this->controller->createWidget(
						'\Widgets\GeoLocationFields\GeoLocationFields'
						, array(
					'id' => 'GeoDDLs',
					'Model' => $Model,
					'ddlCountryAttr' => 'ddlCountry',
					'ddlDivisionAttr' => 'ddlDivision',
					'ddlCityAttr' => 'ddlCity',
					'txtCountryAttr' => 'txtCountry',
					'txtDivisionAttr' => 'txtDivision',
					'txtCityAttr' => 'txtCity',
					'PromptDDLOption' => 'select',
						)
				);
				/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
				\html::PushStateScript('?type=company');
				break;
			case $Model::UserType_Artist:
				$Model->scenario = 'ArtistRegister';
				\html::PushStateScript('?type=artist');
				break;
		}
		#
		$Result = false;
		if (\GPCS::POST('btnRegister') && $ThePost) {
			$Model->attributes = $ThePost;
			$Result = $Model->Register();
			$ActivationCode = $Model->ActivationCode;
		} else
			\Base\FormModel::AjaxValidation('Register', $Model, true);
		#
		if ($Result) {
			\Site\models\User\Activation::SendActivationEmail(
					$ActivationCode
					, $Model->txtEmail
					, $Model->txtUsername);
			\Output::Render($this->controller, '../messages/success', array('msg' => \Lng::Site('tr_user', 'Registered successfully')));
		} else
			\Output::Render($this->controller, 'register'
					, array(
				'Model' => $Model,
				'wdgGeoLocation' => $Model->ddlAccountType == $Model::UserType_Company ? $wdgGeoLocation : null,
					)
			);
	}

}
