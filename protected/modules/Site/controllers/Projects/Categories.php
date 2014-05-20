<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Categories extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Categories'));

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

		$ID = \GPCS::POST('hdnAdditionalID');
		if ($btnDelete && !$ID) { //Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('UserAdditionals');
			$ID = $ID ? $ID['hdnAdditionalID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnAdditionalID' => $ID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('UserAdditionals');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();

		\Output::Render($this->controller
				, ($btnEdit ?
						'editinfo/additionals_addedit' : 'editinfo/additionals')
				, array(
			'Model' => $Model,
			'dg' => $this->DataGrid($this->controller, $Model),
				)
		);
	}

	/**
	 * 
	 * @param \Site\controllers\EditProfile\Additionals $ctrl
	 * @param \Site\models\Profile\Additionals $Model
	 * @return \Base\DataGrid
	 */
	private function DataGrid(\Site\controllers\ProfileController $ctrl, \Site\models\Profile\Additionals $Model) {
		$dg = \html::DataGrid_Ready2('dgAdditionals', 'Site', 'tr_site')
				->DataKey('CombinedID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('Title')
				->header($Model->getAttributeLabel('txtTitle'))
				#
//				, \html::DataGridColumn()
//				->index('Description')
//				->header($Model->getAttributeLabel('ddlYear'))
//				#
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
					$dt = $Model->getdtAdditionals(NULL, true, $DGP);
					if ($dt)
						foreach ($dt as $idx => $dr) {
							$dt[$idx]['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['CombinedID'], "LnkBtn", false, true)
									. \html::ButtonContainer(
											\CHtml::button(\t2::site_site('Edit')
													, array(
												'name' => 'btnEdit',
												'rel' => \html::AjaxElement('#divEditAdditionals', NULL, "hdnAdditionalID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
													)
							));
						}
					return $dt;
				})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$Model->scenario = 'Delete';
					$Model->attributes = array('hdnAdditionalID' => $DGP->RowID);
					return $Model->Delete();
				});
		return $dg;
	}

}
