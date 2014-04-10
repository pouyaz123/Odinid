<?php

namespace Site\controllers\Profile;

use \Consts as C;
use \Tools as T;
use \Site\models\User\Login;
use Site\models\User\Register;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditLocations extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common', \t2::Site_User('Locations'));

		$Model = new \Site\models\Profile\Info('Add');
		$Model->Username = Login::GetSessionDR('Username');

		$Model->Attach_Locations();

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

		$ID = \GPCS::POST('hdnLocationID');
		if ($btnDelete && !$ID) { //Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('ProfileInfo');
			$ID = $ID ? $ID['hdnLocationID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnLocationID' => $ID);

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
			'ddlDivisionAttr' => 'ddlDivision',
			'ddlCityAttr' => 'ddlCity',
			'txtCountryAttr' => 'txtCountry',
			'txtDivisionAttr' => 'txtDivision',
			'txtCityAttr' => 'txtCity',
			'PromptDDLOption' => 'select',
				)
		);
		/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
		\Output::Render($this->controller
				, ($btnEdit ?
						'editinfo/locations_addedit' : 'editinfo/locations')
				, array(
			'Model' => $Model,
			'wdgGeoLocation' => $wdgGeoLocation,
				)
		);
	}

}
