<?php

namespace Site\controllers\Profile;

use \Site\models\User\Login;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditWebAddresses extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common', \t2::Site_User('Web Addresses'));

		$Model = new \Site\models\Profile\Info('Add');
		$Model->Username = Login::GetSessionDR('Username');

		$Model->Attach_WebAddresses();

		$btnAdd = \GPCS::POST('btnAdd');
		$btnSaveEdit = \GPCS::POST('btnSaveEdit');
		$btnEdit = \GPCS::POST('btnEdit');
		$btnDelete = \GPCS::POST('btnDelete');

		if ($btnAdd)
			$Model->scenario = 'Add';
		elseif ($btnEdit || $btnSaveEdit)
			$Model->scenario = 'Edit';
		elseif ($btnDelete)
			$Model->scenario = 'Delete';

		$ID = \GPCS::POST('hdnWebAddrID');
		if ($btnDelete && !$ID) { //Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('ProfileInfo');
			$ID = $ID ? $ID['hdnWebAddrID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnWebAddrID' => $ID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('ProfileInfo');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();
		else
			\Base\FormModel::AjaxValidation('ProfileInfo', $Model, true);

		$wdgGeoLocation = $this->controller->createWidget(
				'\Widgets\GeoLocationFields\GeoLocationFields'
				, array(
			'id' => 'GeoDDLs',
			'Model' => $Model,
			'ddlCountryAttr' => 'ddlCountry',
			'txtCountryAttr' => 'txtCountry',
			'PromptDDLOption' => 'select',
				)
		);
		/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
		\Output::Render($this->controller
				, ($btnEdit ?
						'editinfo/webaddresses_addedit' : 'editinfo/webaddresses')
				, array(
			'Model' => $Model,
			'wdgGeoLocation' => $wdgGeoLocation,
				)
		);
	}

}
