<?php

namespace Admin\controllers\User;

use Consts as C;
use Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class TypesAction extends \CAction {

	public function run() {
		$ctrl = $this->controller;
		/* @var $ctrl \Admin\controllers\UserController */
		$ctrl->pageTitle = \t2::AdminPageTitle('tr_common', 'User types');

		$dg = $this->DataGrid($ctrl);

		$ctrl->SetInternalEnv();
		\html::PushStateScript();
		\Output::Render($ctrl, 'types', array('dg' => $dg));
	}

	private function DataGrid(\Admin\controllers\UserController $ctrl) {
		$FormModel = new \Admin\models\User\Type();
		$fncPassPostParams = function()use($FormModel) {
					$FormModel->attributes = array(
						'txtLogicName' => \GPCS::POST('LogicName'),
						'txtTitle' => \GPCS::POST('Title'),
						'chkIsActive' => \GPCS::POST('IsActive'),
						'chkIsDefault' => \GPCS::POST('IsDefault'),
					);
				};
		$dg = \html::DataGrid_Ready1('dgTypes', 'Admin', 'tr_common')
				->DataKey('ID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
						->direction(\t2::General('LTR_RTL'))
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('t.ID')
				->header(\t2::Admin_Common('ID'))
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('t.LogicName')
				->header($FormModel->getAttributeLabel('txtLogicName'))
				->title(true)
				->editable(true)
				->editrules(array('required' => TRUE, 'length' => '0,' . $FormModel::LogicMaxLen, 'regexp' => $FormModel::LogicPattern))
				#
				, \html::DataGridColumn()
				->index('t.Title')
				->header($FormModel->getAttributeLabel('txtTitle'))
				->title(true)
				->editable(true)
				->editrules(array('required' => TRUE, 'length' => '0,' . $FormModel::TitleMaxLen))
				#
				, \html::DataGridColumn()
				->index('t.IsDefault')
				->type('checkbox')
				->header(\html::PutInATitleTag($FormModel->getAttributeLabel('chkIsDefault')))
				->editable(true)
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('t.IsActive')
				->type('checkbox')
				->header(\html::PutInATitleTag($FormModel->getAttributeLabel('chkIsActive')))
				->editable(true)
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('Actions')
				->header(\t2::Admin_Common('Actions'))
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
									$url = \Yii::app()->createUrl(T\HTTP::URL_InsertGetParams(\Admin\Consts\Routes::User_Permissions, "TypeID={$dr['ID']}"));
									$dt[$idx]['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['ID'], "LnkBtn", $dr['IsUsed'])
											. "<a class='LnkBtn' href='$url'
													rel='AjaxElement:#divUserPermissions' title='" . \t2::Admin_User('Edit Permissions') . "'>
														<img src='/_img/admin/icons/EditPermisions.gif'/>
													</a>"
											. ($dr['IsUsed'] ? '<div class="Info" title="' . \t2::Admin_User("In-use user types can't be removed") . '"></div>' : '');
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
