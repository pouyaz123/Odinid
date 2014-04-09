<?php

namespace Site\controllers\User;

use \Tools as T;
use Site\models\User\Register as Model;
use \Site\Consts\Routes;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Register extends \CAction {

	/**
	 * @param str $code invitation code from $_GET
	 */
	public function run($code = null, $type = null) {
		$Model = new Model();
		$ThePost = \GPCS::POST('Register');
		#ini attrs
		$Model->attributes = array(
			'txtInvitationCode' => $code,
			'ddlAccountType' => isset($ThePost['ddlAccountType']) ?
					$ThePost['ddlAccountType'] :
					($type ? ucfirst($type) : $Model->ddlAccountType),
		);
		switch ($Model->ddlAccountType) {
			case Model::UserAccountType_Company:
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
				\html::PushStateScript(Routes::UserRegister . '?type=company');
				$this->controller->pageTitle = \t2::SitePageTitle('tr_user', 'Register Company');
				break;
			case Model::UserAccountType_Artist:
				$Model->scenario = 'ArtistRegister';
				\html::PushStateScript(Routes::UserRegister . '?type=artist');
				$this->controller->pageTitle = \t2::SitePageTitle('tr_user', 'Register Artist');
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
			\Output::Render($this->controller, '../messages/success'
					, array(
				'msg' => \t2::Site_User('Registered successfully')
				. ' <br/> '
				. \t2::Site_User('Activation link sent successfully')
					)
			);
		} else
			\Output::Render($this->controller, 'register'
					, array(
				'Model' => $Model,
				'wdgGeoLocation' => $Model->ddlAccountType == Model::UserAccountType_Company ? $wdgGeoLocation : null,
					)
			);
	}

}
