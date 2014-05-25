<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Educations extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Educations'));
		\html::TagIt_Load();
		\html::jqUI_AutoComplete_Load();
		\html::DatePicker_Load();

		$Model = new \Site\models\Profile\Educations('Add');
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

		$ID = \GPCS::POST('hdnEducationID');
		if ($btnDelete && !$ID) { //Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('UserEducations');
			$ID = $ID ? $ID['hdnEducationID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnEducationID' => $ID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('UserEducations');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();
		else {//school name autocomplete
			\Output::AddIn_AjaxOutput(function() {
				$term = \GPCS::GET('term')? : \GPCS::POST('term');
				if ($term) {
					$dt = T\DB::GetTable("SELECT `Title`, `URL`, `ID`"
									. " FROM `_school_info`"
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
			}, 'AutoComplete_UserEducations_txtSchoolTitle');
			\Output::AddIn_AjaxOutput(function() {
				$term = \GPCS::GET('term')? : \GPCS::POST('term');
				if ($term) {
					$dt = T\DB::GetTable("SELECT `StudyField`"
									. " FROM `_education_studyfields`"
									. " WHERE `StudyField` LIKE CONCAT(:term, '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
									, array(':term' => T\DB::EscapeLikeWildCards($term)));
					if ($dt) {
						foreach ($dt as $idx => $dr)
							$dt[$idx] = $dr['StudyField'];
						echo json_encode($dt);
					}
				}
			}, 'AutoComplete_UserEducations_txtStudyField');
			\Output::AddIn_AjaxOutput(function() {
				$term = \GPCS::GET('term')? : \GPCS::POST('term');
				if ($term) {
					$dt = T\DB::GetTable("SELECT `Degree`"
									. " FROM `_education_degrees`"
									. " WHERE `Degree` LIKE CONCAT(:term, '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
									, array(':term' => T\DB::EscapeLikeWildCards($term)));
					if ($dt) {
						foreach ($dt as $idx => $dr)
							$dt[$idx] = $dr['Degree'];
						echo json_encode($dt);
					}
				}
			}, 'AutoComplete_UserEducations_txtDegree');
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
						'editinfo/educations_addedit' : 'editinfo/educations')
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
	 * @param \Site\models\Profile\Educations $Model
	 * @return \Base\DataGrid
	 */
	private function DataGrid(\Site\controllers\ProfileController $ctrl, \Site\models\Profile\Educations $Model) {
		$dg = \html::DataGrid_Ready2('dgEducations', 'Site', 'tr_site')
				->DataKey('CombinedID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('si.Title')
				->name('SchoolTitle')
				->header($Model->getAttributeLabel('txtSchoolTitle'))
				#
				, \html::DataGridColumn()
				->index('Country')
				->whereclause_leftside('IFNULL(gc.`AsciiName`, guc.`Country`)')
				->name('Country')
				->header($Model->getAttributeLabel('ddlCountry'))
				#
				, \html::DataGridColumn()
				->index('StudyField')
				->header($Model->getAttributeLabel('txtStudyField'))
				#
				, \html::DataGridColumn()
				->index('Degree')
				->header($Model->getAttributeLabel('txtDegree'))
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
					$dt = $Model->getdtEducations(NULL, true, $DGP);
					if ($dt)
						foreach ($dt as $idx => $dr) {
							$dr['SchoolTitle'] = "<div title='{$dr['SchoolURL']}'>{$dr['SchoolTitle']}</div>";
							$dr['Country'] = "<div title='"
									. \CHtml::encode($dr['City'] . ($dr['City'] && $dr['Division'] ? ' , ' : '') . $dr['Division'])
									. "'>{$dr['Country']}</div>";
							$dr['ToDate'] = $dr['ToPresent'] ? \t2::site_site('Present') : $dr['ToDate'];
							$dr['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['CombinedID'], "LnkBtn", false, true)
									. \html::ButtonContainer(
											\CHtml::button(\t2::site_site('Edit')
													, array(
												'name' => 'btnEdit',
												'rel' => \html::AjaxElement('#divEditEducations', NULL, "hdnEducationID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
													)
							));
							$dt[$idx] = $dr;
						}
					return $dt;
				})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$Model->scenario = 'Delete';
					$Model->attributes = array('hdnEducationID' => $DGP->RowID);
					return $Model->Delete();
				});
		return $dg;
	}

}
