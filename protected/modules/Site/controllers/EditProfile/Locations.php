<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Locations extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Locations'));

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
			'dg' => $this->DataGrid($this->controller, $Model),
				)
		);
	}

	/**
	 * 
	 * @param \Site\controllers\ProfileController $ctrl
	 * @param \Site\models\Profile\Info $Model
	 * @return \Base\DataGrid
	 */
	private function DataGrid(\Site\controllers\ProfileController $ctrl, \Site\models\Profile\Info $Model) {
		$dg = \html::DataGrid_Ready2('dgLocations', 'Site', 'tr_site')
				->DataKey('CombinedID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('Country')
				->whereclause_leftside('IFNULL(gc.`AsciiName`, guc.`Country`)')
				->header($Model->getAttributeLabel('ddlCountry'))
				#
				, \html::DataGridColumn()
				->index('Division')
				->whereclause_leftside('IFNULL(gd.`AsciiName`, gud.`Division`)')
				->header($Model->getAttributeLabel('ddlDivision'))
				#
				, \html::DataGridColumn()
				->index('City')
				->whereclause_leftside('IFNULL(gct.`AsciiName`, guct.`City`)')
				->header($Model->getAttributeLabel('ddlCity'))
				#
				, \html::DataGridColumn()
				->index('IsCurrentLocation')
				->type('checkbox')
				->header($Model->getAttributeLabel('chkIsCurrentLocation'))
				#
				, \html::DataGridColumn()
				->index('IsBillingLocation')
				->type('checkbox')
				->header($Model->getAttributeLabel('chkIsBillingLocation'))
				#
				, \html::DataGridColumn()
				->index('Actions')
				->header(\t2::site_site('Actions'))
				->search(false)
				->editable(FALSE)
				->sortable(false)
				->title(false)
				->width('75px')
		);
		$dg
				->SelectQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$dt = $Model->asa('Info_Locations')->getdtLocations(NULL, true, $DGP);
					if ($dt)
						foreach ($dt as $idx => $dr) {
							$dr['Country'] = "<div title='"
									. \CHtml::encode(
											($dr['Address1'] ? $Model->getAttributeLabel('txtAddress1') . ' : ' . $dr['Address1'] . '<br/>' : '')
											. ($dr['Address2'] ? $Model->getAttributeLabel('txtAddress2') . ' : ' . $dr['Address2'] . '<br/>' : '')
											. ($dr['PostalCode'] ? $Model->getAttributeLabel('txtPostalCode') . ' : ' . $dr['PostalCode'] . '<br/>' : ''))
									. "'>{$dr['Country']}</div>";
							$dr['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['CombinedID'], "LnkBtn", false, true)
									. \html::ButtonContainer(
											\CHtml::button(\t2::site_site('Edit')
													, array(
												'name' => 'btnEdit',
												'rel' => \html::AjaxElement('#divEditLocation', NULL, "hdnLocationID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
													)
							));
							$dt[$idx] = $dr;
						}
					return $dt;
				})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$Model->scenario = 'Delete';
					$Model->attributes = array('hdnLocationID' => $DGP->RowID);
					return $Model->Delete();
				});
		return $dg;
	}

}
