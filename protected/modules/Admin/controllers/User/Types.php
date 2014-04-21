<?php

namespace Admin\controllers\User;

use \Consts as C;
use \Tools as T;
use \Admin\models\User\Type as Model;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Types extends \CAction {

	public function run() {
		$ctrl = $this->controller;
		/* @var $ctrl \Admin\controllers\UserController */
		$ctrl->pageTitle = \t2::AdminPageTitle('User types');

		$dg = $this->DataGrid($ctrl);

		\Output::Render($ctrl, 'types', array('dg' => $dg));
	}

	private function DataGrid(\Admin\controllers\UserController $ctrl) {
		$Model = new Model();
		$fncPassPostParams = function()use($Model) {
					$Model->attributes = array(
						'txtLogicName' => \GPCS::POST('LogicName'),
						'txtTitle' => \GPCS::POST('Title'),
						'chkIsActive' => \GPCS::POST('IsActive'),
						'chkIsDefault' => \GPCS::POST('IsDefault'),
					);
				};
		$dg = \html::DataGrid_Ready1('dgTypes', 'Admin', 'tr_admin')
				->DataKey('ID')
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
						->direction(\t2::General('LTR_RTL'))
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('t.ID')
				->header(\t2::admin_admin('ID'))
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('t.LogicName')
				->header($Model->getAttributeLabel('txtLogicName'))
				->title(true)
				->editable(true)
				->editrules(array('required' => TRUE, 'length' => '0,' . Model::LogicMaxLen, 'regexp' => Model::LogicPattern))
				#
				, \html::DataGridColumn()
				->index('t.Title')
				->header($Model->getAttributeLabel('txtTitle'))
				->title(true)
				->editable(true)
				->editrules(array('required' => TRUE, 'length' => '0,' . Model::TitleMaxLen))
				#
				, \html::DataGridColumn()
				->index('t.IsDefault')
				->type('checkbox')
				->header(\html::PutInATitleTag($Model->getAttributeLabel('chkIsDefault')))
				->editable(true)
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('t.IsActive')
				->type('checkbox')
				->header(\html::PutInATitleTag($Model->getAttributeLabel('chkIsActive')))
				->editable(true)
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('Actions')
				->header(\t2::admin_admin('Actions'))
				->search(false)
				->editable(FALSE)
				->sortable(false)
				->title(false)
				->width('75px')
		);
		$dg
				->SelectQuery(function(\Base\DataGridParams $DGP)use($Model) {
							$Model->scenario = 'select';
							$dt = $Model->Select($DGP);
							if ($dt)
								foreach ($dt as $idx => $dr) {
									$url = \Yii::app()->createUrl(T\HTTP::URL_InsertGetParams(\Admin\Consts\Routes::User_Permissions, "TypeID={$dr['ID']}"));
									$dt[$idx]['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['ID'], "LnkBtn", $dr['IsUsed'])
											. "<a class='LnkBtn' href='$url'
													rel='AjaxElement:#divUserPermissions' title='" . \t2::admin_admin('Edit Permissions') . "'>
														<img src='/_img/admin/icons/EditPermisions.gif'/>
													</a>"
											. ($dr['IsUsed'] ? '<div class="Info" title="' . \t2::admin_admin("In-use user types can't be removed") . '"></div>' : '');
								}
							return $dt;
						})
				->InsertQuery(function(\Base\DataGridParams $DGP)use($Model, $fncPassPostParams) {
							$Model->scenario = 'insert';
							$fncPassPostParams();
							return $Model->Insert($DGP);
						})
				->UpdateQuery(function(\Base\DataGridParams $DGP)use($Model, $fncPassPostParams) {
							$Model->scenario = 'update';
							$fncPassPostParams();
							return $Model->Update($DGP);
						})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($Model) {
							$Model->scenario = 'delete';
							return $Model->Delete($DGP);
						});
		return $dg;
	}

}
