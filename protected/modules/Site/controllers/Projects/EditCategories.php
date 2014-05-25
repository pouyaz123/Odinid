<?php

namespace Site\controllers\Projects;

use \Site\models\User\Login;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditCategories extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Project Categories'));

		$Model = new \Site\models\Projects\Categories('Add');
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

		$ID = \GPCS::POST('hdnID');
		if ($btnDelete && !$ID) { //Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('PrjCat');
			$ID = $ID ? $ID['hdnID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnID' => $ID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('PrjCat');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();

		\Output::Render($this->controller
				, ($btnEdit ?
						'/projects/cats_addedit' : '/projects/cats')
				, array(
			'Model' => $Model,
			'dg' => $this->DataGrid($this->controller, $Model),
				)
		);
	}

	/**
	 * 
	 * @param \Site\controllers\ProfileController $ctrl
	 * @param \Site\models\Profile\Categories $Model
	 * @return \Base\DataGrid
	 */
	private function DataGrid(\Site\controllers\ProfileController $ctrl, \Site\models\Projects\Categories $Model) {
		$dg = \html::DataGrid_Ready2('dgCategories', 'Site', 'tr_site')
				->DataKey('ID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('Title')
				->header($Model->getAttributeLabel('txtTitle'))
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
					$dt = $Model->getdtCategories(NULL, true, $DGP);
					if ($dt) {
						foreach ($dt as $idx => $dr) {
							$dt[$idx]['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['ID'], "LnkBtn", false, true)
									. \html::ButtonContainer(
											\CHtml::button(\t2::site_site('Edit')
													, array(
												'name' => 'btnEdit',
												'rel' => \html::AjaxElement('#divEditCategories', NULL, "hdnID={$dr['ID']}") . \html::SimpleAjaxPanel,
													)
							));
						}
					}
					return $dt;
				})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$Model->scenario = 'Delete';
					$Model->attributes = array('hdnID' => $DGP->RowID);
					return $Model->Delete();
				});
		return $dg;
	}

}
