<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Certificates extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Certificates'));
		\html::TagIt_AC_URLFactor();
		\html::jqUI_AutoComplete_Load();
		\html::DatePicker_Load();

		$Model = new \Site\models\Profile\Certificates('Add');
		$Model->UserID = Login::GetSessionDR('ID');

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

		$ID = \GPCS::POST('hdnCertificateID');
		if ($btnDelete && !$ID) { //Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('UserCertificates');
			$ID = $ID ? $ID['hdnCertificateID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnCertificateID' => $ID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('UserCertificates');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();
		else {//institution name autocomplete
			\Output::AddIn_AjaxOutput(function() {
				$term = \GPCS::GET('term')? : \GPCS::POST('term');
				if ($term) {
					$dt = T\DB::GetTable("SELECT `Title`, `URL`, `ID`"
									. " FROM `_institutions`"
									. " WHERE `Title` LIKE CONCAT(" . T\DB::MySQLConvert(':term', 2) . ", '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
									, array(':term' => T\DB::EscapeLikeWildCards($term)));
					if ($dt) {
						foreach ($dt as $idx => $dr) {
							$item = array(
								'label' => "<div rel='" . json_encode(array('ID' => $dr['ID'], 'URL' => $dr['URL'])) . "'>{$dr['Title']}" . ($dr['URL'] ? " ({$dr['URL']})" : '') . "</div>"
								, 'value' => $dr['Title']);
							$dt[$idx] = $item;
						}
						echo json_encode($dt);
					}
				}
			}, 'AutoComplete_UserCertificates_txtInstitutionTitle');
		}

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
						'editinfo/certificates_addedit' : 'editinfo/certificates')
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
	 * @param \Site\models\Profile\Certificates $Model
	 * @return \Base\DataGrid
	 */
	private function DataGrid(\Site\controllers\ProfileController $ctrl, \Site\models\Profile\Certificates $Model) {
		$dg = \html::DataGrid_Ready2('dgCertificates', 'Site', 'tr_site')
				->DataKey('CombinedID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('ins.Title')
				->name('InstitutionTitle')
				->header($Model->getAttributeLabel('txtInstitutionTitle'))
				#
				, \html::DataGridColumn()
				->index('Country')
				->whereclause_leftside('IFNULL(gc.`AsciiName`, guc.`Country`)')
				->name('Country')
				->header($Model->getAttributeLabel('ddlCountry'))
				#
				, \html::DataGridColumn()
				->index('Title')
				->header($Model->getAttributeLabel('txtTitle'))
				#
				, \html::DataGridColumn()
				->index('Date')
				->type('date')
				->header($Model->getAttributeLabel('txtDate'))
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
					$dt = $Model->getdtCertificates(NULL, true, $DGP);
					if ($dt)
						foreach ($dt as $idx => $dr) {
							$dr['InstitutionTitle'] = "<div title='" . \CHtml::encode($dr['InstitutionURL']) . "'>{$dr['InstitutionTitle']}</div>";
							$dr['Country'] = "<div title='"
									. \CHtml::encode($dr['City'] . ($dr['City'] && $dr['Division'] ? ' , ' : '') . $dr['Division'])
									. "'>{$dr['Country']}</div>";
							$dr['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['CombinedID'], "LnkBtn", false, true)
									. \html::ButtonContainer(
											\CHtml::button(\t2::site_site('Edit')
													, array(
												'name' => 'btnEdit',
												'rel' => \html::AjaxElement('#divEditCertificates', NULL, "hdnCertificateID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
													)
							));
							$dt[$idx] = $dr;
						}
					return $dt;
				})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$Model->scenario = 'Delete';
					$Model->attributes = array('hdnCertificateID' => $DGP->RowID);
					return $Model->Delete();
				});
		return $dg;
	}

}
