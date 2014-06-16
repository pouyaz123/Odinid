<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Awards extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Awards'));
		\html::TagIt_AC_Companies_Load();
		\html::jqUI_AutoComplete_Load();

		$Model = new \Site\models\Profile\Awards('Add');
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

		$ID = \GPCS::POST('hdnAwardID');
		if ($btnDelete && !$ID) { //Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('UserAwards');
			$ID = $ID ? $ID['hdnAwardID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnAwardID' => $ID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('UserAwards');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();
		else {//organization name autocomplete
			\Output::AddIn_AjaxOutput(function() {
				$term = \GPCS::GET('term')? : \GPCS::POST('term');
				if ($term) {
					$dt = T\DB::GetTable("SELECT `Title`, `URL`, `ID`"
									. " FROM `_organizations`"
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
			}, 'AutoComplete_UserAwards_txtOrganizationTitle');
		}

		\Output::Render($this->controller
				, ($btnEdit ?
						'editinfo/awards_addedit' : 'editinfo/awards')
				, array(
			'Model' => $Model,
			'dg' => $this->DataGrid($this->controller, $Model),
				)
		);
	}

	/**
	 * 
	 * @param \Site\controllers\ProfileController $ctrl
	 * @param \Site\models\Profile\Awards $Model
	 * @return \Base\DataGrid
	 */
	private function DataGrid(\Site\controllers\ProfileController $ctrl, \Site\models\Profile\Awards $Model) {
		$dg = \html::DataGrid_Ready2('dgAwards', 'Site', 'tr_site')
				->DataKey('CombinedID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('org.Title')
				->name('OrganizationTitle')
				->header($Model->getAttributeLabel('txtOrganizationTitle'))
				#
				, \html::DataGridColumn()
				->index('Title')
				->header($Model->getAttributeLabel('txtTitle'))
				#
				, \html::DataGridColumn()
				->index('Year')
				->header($Model->getAttributeLabel('ddlYear'))
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
					$dt = $Model->getdtAwards(NULL, true, $DGP);
					if ($dt)
						foreach ($dt as $idx => $dr) {
							$dr['OrganizationTitle'] = "<div title='"
									. \CHtml::encode($dr['OrganizationURL'])
									. "'>{$dr['OrganizationTitle']}</div>";
							$dr['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['CombinedID'], "LnkBtn", false, true)
									. \html::ButtonContainer(
											\CHtml::button(\t2::site_site('Edit')
													, array(
												'name' => 'btnEdit',
												'rel' => \html::AjaxElement('#divEditAwards', NULL, "hdnAwardID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
													)
							));
							$dt[$idx] = $dr;
						}
					return $dt;
				})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$Model->scenario = 'Delete';
					$Model->attributes = array('hdnAwardID' => $DGP->RowID);
					return $Model->Delete();
				});
		return $dg;
	}

}
