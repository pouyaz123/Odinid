<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Experiences extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Experiences'));
		\html::TagIt_AC_URLFactor();
		\html::jqUI_AutoComplete_Load();
		\html::DatePicker_Load();

		$Model = new \Site\models\Profile\Experiences('Add');
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

		$ID = \GPCS::POST('hdnExperienceID');
		if ($btnDelete && !$ID) { //Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('UserExperiences');
			$ID = $ID ? $ID['hdnExperienceID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnExperienceID' => $ID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('UserExperiences');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();
		else {//company name autocomplete
			\Output::AddIn_AjaxOutput(function() {
				$term = \GPCS::GET('term')? : \GPCS::POST('term');
				if ($term) {
					$dt = T\DB::GetTable("SELECT `Title`, `URL`, `ID`"
									. " FROM `_company_info`"
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
			}, 'AutoComplete_UserExperiences_txtCompanyTitle');
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
						'editinfo/experiences_addedit' : 'editinfo/experiences')
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
	 * @param \Site\models\Profile\Experiences $Model
	 * @return \Base\DataGrid
	 */
	private function DataGrid(\Site\controllers\ProfileController $ctrl, \Site\models\Profile\Experiences $Model) {
		$dg = \html::DataGrid_Ready2('dgExperiences', 'Site', 'tr_site')
				->DataKey('CombinedID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('ci.Title')
				->name('CompanyTitle')
				->header($Model->getAttributeLabel('txtCompanyTitle'))
				#
				, \html::DataGridColumn()
				->index('Country')
				->whereclause_leftside('IFNULL(gc.`AsciiName`, guc.`Country`)')
				->name('Country')
				->header($Model->getAttributeLabel('ddlCountry'))
				#
				, \html::DataGridColumn()
				->index('JobTitle')
				->header($Model->getAttributeLabel('txtJobTitle'))
				#
				, \html::DataGridColumn()
				->index('FromDate')
				->type('date')
				->header($Model->getAttributeLabel('txtFromDate'))
				#
				, \html::DataGridColumn()
				->index('ToDate')
				->type('date')
				->header($Model->getAttributeLabel('txtToDate'))
				#
				, \html::DataGridColumn()
				->index('HealthInsurance')
				->type('checkbox')
				->header($Model->getAttributeLabel('chkHealthInsurance'))
				#
				, \html::DataGridColumn()
				->index('RetirementAccount')
				->type('checkbox')
				->header($Model->getAttributeLabel('chkRetirementAccount'))
				#
				, \html::DataGridColumn()
				->index('Actions')
				->header(\t2::site_site('Actions'))
				->search(false)
				->editable(FALSE)
				->sortable(false)
				->title(false)
				->width('100px')
		);
		$dg
				->SelectQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$dt = $Model->getdtExperiences(NULL, true, $DGP);
					if ($dt)
						foreach ($dt as $idx => $dr) {
							$dr['CompanyTitle'] = "<div title='{$dr['CompanyURL']}'>{$dr['CompanyTitle']}</div>";
							$dr['Country'] = "<div title='"
									. \CHtml::encode($dr['City'] . ($dr['City'] && $dr['Division'] ? ' , ' : '') . $dr['Division'])
									. "'>{$dr['Country']}</div>";
							$dr['ToDate'] = $dr['ToPresent'] ? \t2::site_site('Present') : $dr['ToDate'];
							$dr['JobTitle'] = "<div title='"
									. \CHtml::encode(
											($dr['Level'] ? $Model->getAttributeLabel('ddlLevel') . ' : ' . $dr['Level'] . '<br/>' : '')
											. ($dr['EmploymentType'] ? $Model->getAttributeLabel('ddlEmploymentType') . ' : ' . $dr['EmploymentType'] . '<br/>' : '')
											. ($dr['SalaryType'] ? $Model->getAttributeLabel('ddlSalaryType') . ' : ' . $dr['SalaryType'] . '<br/>' : '')
											. ($dr['SalaryAmount'] ? $Model->getAttributeLabel('txtSalaryAmount') . ' : ' . $dr['SalaryAmount'] . '<br/>' : '')
									)
									. "'>"
									. "{$dr['JobTitle']}"
									. "</div>";
							$dr['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['CombinedID'], "LnkBtn", false, true)
									. \html::ButtonContainer(
											\CHtml::button(\t2::site_site('Edit')
													, array(
												'name' => 'btnEdit',
												'rel' => \html::AjaxElement('#divEditExperiences', NULL, "hdnExperienceID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
													)
							));
							$dt[$idx] = $dr;
						}
					return $dt;
				})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$Model->scenario = 'Delete';
					$Model->attributes = array('hdnExperienceID' => $DGP->RowID);
					return $Model->Delete();
				});
		return $dg;
	}

}
