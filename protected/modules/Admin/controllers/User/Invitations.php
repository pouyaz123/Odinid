<?php

namespace Admin\controllers\User;

use \Consts as C;
use \Tools as T;
use Admin\models\User\Invitation as Model;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Invitations extends \CAction {

	public function run() {
		$ctrl = $this->controller;
		/* @var $ctrl \Admin\controllers\UserController */
		$ctrl->pageTitle = \t2::AdminPageTitle('tr_common', 'Invitations');

		$dg = $this->DataGrid($ctrl);

		\html::PushStateScript();
		\Output::Render($ctrl, 'invitations', array('dg' => $dg));
	}

	private function DataGrid(\Admin\controllers\UserController $ctrl) {
		$Model = new Model();
		$fncPassPostParams = function()use($Model) {
			$Model->attributes = array(
				'txtCode' => \GPCS::POST('Code'),
				'ddlUserTypeID' => \GPCS::POST('UserTypeID'),
				'txtUserTypeExpDate' => \GPCS::POST('UserTypeExpDate'),
				'txtInvitationExpDate' => \GPCS::POST('InvitationExpDate'),
				'txtDescription' => \GPCS::POST('Description'),
			);
		};
		$ActiveUserTypes = \Admin\models\User\Type::GetActiveUserTypes();
		$strDDL_Types = \Base\DataGrid::CreateDDLElements(
						$ActiveUserTypes
						, 'ID'
						, 'Title');
		$dg = \html::DataGrid_Ready1('dgInvitations', 'Admin', 'tr_common')
				->DataKey('ID')
				->SetNavigator(array(
					'del' => true
				))
				->Options(
						\html::DataGridConfig()
						->caption($ctrl->pageTitle)
						->direction(\t2::General('LTR_RTL'))
				)
				->SetColumns(
				\html::DataGridColumn()
				->index('ID')
				->header(\t2::Admin_Common('ID'))
				->width('50px')
				#
				, \html::DataGridColumn()
				->index('Code')
				->header($Model->getAttributeLabel('txtCode'))
				->title(true)
				->editable(true)
				->editrules(array('required' => TRUE, 'length' => '0,' . \ValidationLimits\User::GetInstance()->InvitationCode['max']))
				->width('100px')
				#
				, \html::DataGridColumn()
				->index('InvitationExpDate')
				->header(\html::PutInATitleTag($Model->getAttributeLabel('txtInvitationExpDate')))
				->title(true)
				->editable(true)
				->type('date')
				->searchoptions(array('searchOnEnter' => true))
				->width('100px')
				#
				, \html::DataGridColumn()
				->index('UserTypeID')
				->header($Model->getAttributeLabel('ddlUserTypeID'))
				->editable(true)
				->type('select')
				->editoptions(array('value' => $strDDL_Types))
				->editrules(array('required' => TRUE))
				->width('75px')
				#
				, \html::DataGridColumn()
				->index('UserTypeExpDate')
				->header(\html::PutInATitleTag($Model->getAttributeLabel('txtUserTypeExpDate')))
				->title(true)
				->editable(true)
				->type('date')
				->searchoptions(array('searchOnEnter' => true))
				->width('100px')
				#
				, \html::DataGridColumn()
				->index('Description')
				->header($Model->getAttributeLabel('txtDescription'))
				->title(true)
				->editable(true)
				->editoptions(array('class' => '')) //prevent center align
				->editrules(array('length' => '0,' . Model::DescriptionMaxLen))
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
				->SelectQuery(function(\Base\DataGridParams $DGP)use($Model) {
					$Model->scenario = 'select';
					$dt = $Model->Select($DGP);
					if ($dt)
						foreach ($dt as $idx => $dr) {
							$dt[$idx]['Actions'] = $DGP->DataGrid->GetActionColButtons($dr['ID'], "LnkBtn");
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
