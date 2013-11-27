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
		$ctrl->pageTitle = \Lng::AdminPageTitle('Modules', 'User types');

		$dg = $this->DataGrid($ctrl);

		$ctrl->SetInternalEnv();
		\html::PushStateScript();
		\Output::Render($ctrl, 'types', array('dg' => $dg));
	}

	private function DataGrid(\Admin\controllers\UserController $ctrl) {
		$TypeForm = new \Admin\models\User\TypeForm();
		$fncPassPostParams = function()use($TypeForm) {
					$TypeForm->attributes = array(
						'txtLogicName' => \GPCS::POST('LogicName'),
						'txtTitle' => \GPCS::POST('Title'),
						'chkIsActive' => \GPCS::POST('IsActive'),
						'chkIsDefault' => \GPCS::POST('IsDefault'),
					);
				};
		$dgTypes = \html::DataGrid('dgTypes')
				->DataKey('ID')
				->SetTranslation('Admin', 'Modules')
				->Resources(array(
					'Edit' => '<img src="/_img/icons/edit_inline16.png"/>'
					, 'Delete' => '<img src="/_img/icons/bin16.png"/>'
					, 'Cancel' => '<img src="/_img/icons/cancel16.png"/>'
					, 'Save' => '<img src="/_img/icons/save16.png"/>'))
				->SetFilterBar(array('searchOnEnter' => false))
				->SetNavigator(array(
					'search' => false
					, 'edit' => true
					, 'del' => false
					, 'saveall' => true
					, 'add' => true
				))
				->Resizable()
				->SetDblClickEdit()
				->setTableClasses('MidAlign')
				->Options(
						\html::DataGridConfig()
						->autowidth(true)
						->multiselect(true)
						->caption($ctrl->pageTitle)
						->direction(\Lng::Admin('Common', 'res_Direction'))
						->cmTemplate(array(
							'align' => 'center'
							, 'title' => FALSE
							, 'search' => true
							, 'editable' => false
							, 'sortable' => true
							, 'editoptions' => array('class' => 'CenterAlign')
						))
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('t.ID')
				->header(\Lng::Admin('Common', 'ID'))
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('t.LogicName')
				->header($TypeForm->getAttributeLabel('txtLogicName'))
				->editable(true)
				->editoptions(array('class' => 'ltr CenterAlign'))
				->editrules(array('required' => TRUE, 'length' => '0,' . $TypeForm::LogicMaxLen, 'regexp' => $TypeForm::LogicPattern))
				#
				, \html::DataGridColumn()
				->index('t.Title')
				->header($TypeForm->getAttributeLabel('txtTitle'))
				->editable(true)
				->editrules(array('required' => TRUE, 'length' => '0,' . $TypeForm::TitleMaxLen))
				#
				, \html::DataGridColumn()
				->index('t.IsDefault')
				->type('checkbox')
				->header(\html::PutInATitleTag($TypeForm->getAttributeLabel('chkIsDefault')))
				->editable(true)
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('t.IsActive')
				->type('checkbox')
				->header(\html::PutInATitleTag($TypeForm->getAttributeLabel('chkIsActive')))
				->editable(true)
//								->editoptions(array('checked' => true))	//todo1: bug when is checked in the edit mode it will turn back to checked even if it was not checked in Database
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('Actions')
				->header(\Lng::Admin('Common', 'Actions'))
				->search(false)
				->editable(FALSE)
				->sortable(false)
				->title(false)
		);
		$dgTypes
				->SelectQuery(function(\Base\DataGridParams $DGP)use($TypeForm) {
							$TypeForm->scenario = 'select';
							$dt = $TypeForm->Select($DGP);
							foreach ($dt as $idx => $dr) {
								$url = \Yii::app()->createUrl(T\HTTP::URL_InsertGetParams(\Admin\Consts\Routes::User_Permissions, "TypeID={$dr['ID']}"));
								$dt[$idx]['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['ID'], "LnkBtn", isset($dr['IsUsed']))
										. "<a class='LnkBtn' href='$url'
													rel='AjaxElement:#divUserPermissions' title='" . \Lng::Admin('User', 'Edit Permissions') . "'>
														<img src='/_img/admin/icons/EditPermisions.gif'/>
													</a>"
										. (isset($dr['IsUsed']) ? '<div class="Info" title="' . \Lng::Admin('User', "In-use user types can't be removed") . '"></div>' : '');
							}
							return $dt;
						})
				->InsertQuery(function(\Base\DataGridParams $DGP)use($TypeForm, $fncPassPostParams) {
							$TypeForm->scenario = 'insert';
							$fncPassPostParams();
							return $TypeForm->Insert($DGP);
						})
				->UpdateQuery(function(\Base\DataGridParams $DGP)use($TypeForm, $fncPassPostParams) {
							$TypeForm->scenario = 'update';
							$fncPassPostParams();
							return $TypeForm->Update($DGP);
						})
				->DeleteQuery(function(\Base\DataGridParams $DGP)use($TypeForm) {
							$TypeForm->scenario = 'delete';
							return $TypeForm->Delete($DGP);
						});
		return $dgTypes;
	}

}

?>
