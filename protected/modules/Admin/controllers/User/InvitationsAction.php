<?php

namespace Admin\controllers\User;

use Consts as C;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class InvitationsAction extends \CAction {

	public function run() {
		$ctrl = $this->controller;
		/* @var $ctrl \Admin\controllers\UserController */
		$ctrl->pageTitle = \Lng::AdminPageTitle('tr_Common', 'Invitations');

		$dg = $this->DataGrid($ctrl);

		$ctrl->SetInternalEnv();
		\html::PushStateScript();
		\Output::Render($ctrl, 'invitations', array('dg' => $dg));
	}

	private function DataGrid(\Admin\controllers\UserController $ctrl) {
		$FormModel = new \Admin\models\User\Invitation();
		$fncPassPostParams = function()use($FormModel) {
					$FormModel->attributes = array(
						'txtCode' => \GPCS::POST('Code'),
						'ddlUserTypeID' => \GPCS::POST('UserTypeID'),
						'txtDescription' => \GPCS::POST('Description'),
					);
				};
		$ActiveUserTypes = \Admin\models\User\Type::GetActiveUserTypes();
		$strDDL_Types = \Base\DataGrid::CreateDDLElements(
						$ActiveUserTypes
						, 'ID'
						, 'Title');
		$dg = \html::DataGrid_Ready1('dgInvitations', 'Admin', 'tr_Common')
				->DataKey('ID')
				->SetNavigator(array(
					'del' => true
				))
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
						->direction(\Lng::Admin('tr_Common', 'LTR_RTL'))
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('ID')
				->header(\Lng::Admin('tr_Common', 'ID'))
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('UserTypeID')
				->header($FormModel->getAttributeLabel('ddlUserTypeID'))
				->editable(true)
				->type('select')
				->editoptions(array('value' => $strDDL_Types))
				->editrules(array('required' => TRUE))
				->width('75px')
				#
				, \html::DataGridColumn()
				->index('Code')
				->header($FormModel->getAttributeLabel('txtCode'))
				->title(true)
				->editable(true)
				->editrules(array('required' => TRUE, 'length' => '0,' . $FormModel::CodeMaxLen))
				->width('100px')
				#
				, \html::DataGridColumn()
				->index('Description')
				->header($FormModel->getAttributeLabel('txtDescription'))
				->title(true)
				->editable(true)
				->editoptions(array('class' => '')) //prevent center align
				->editrules(array('length' => '0,' . $FormModel::DescriptionMaxLen))
				#
				, \html::DataGridColumn()
				->index('Actions')
				->header(\Lng::Admin('tr_Common', 'Actions'))
				->search(false)
				->editable(FALSE)
				->sortable(false)
				->title(false)
				->width('75px')
		);
		$dg
				->SelectQuery(function(\Base\DataGridParams $DGP)use($FormModel) {
							$FormModel->scenario = 'select';
							$dt = $FormModel->Select($DGP);
							if ($dt)
								foreach ($dt as $idx => $dr) {
									$dt[$idx]['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['ID'], "LnkBtn");
								}
							return $dt;
						})
				->InsertQuery(function(\Base\DataGridParams $DGP)use($FormModel, $fncPassPostParams) {
							$FormModel->scenario = 'insert';
							$fncPassPostParams();
							return $FormModel->Insert($DGP);
						})
				->UpdateQuery(function(\Base\DataGridParams $DGP)use($FormModel, $fncPassPostParams) {
							$FormModel->scenario = 'update';
							$fncPassPostParams();
							return $FormModel->Update($DGP);
						})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($FormModel) {
							$FormModel->scenario = 'delete';
							return $FormModel->Delete($DGP);
						});
		return $dg;
	}

}

?>
