<?php

namespace Site\controllers\Profile;

use \Tools as T;
use \Site\models\User\Login;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditEmails extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common', \t2::Site_User('Emails'));

		$Model = new \Site\models\Profile\Info('Add');
		$Model->Username = Login::GetSessionDR('Username');

		$Model->Attach_Emails();

		$btnAdd = \GPCS::POST('btnAdd');
		$btnSaveEdit = \GPCS::POST('btnSaveEdit');
		$btnEdit = \GPCS::POST('btnEdit');
		$btnDelete = \GPCS::POST('btnDelete');
		$btnResendActivationLink = \GPCS::POST('btnResendActivationLink');
		$btnPrimary = \GPCS::POST('btnPrimary');

		if ($btnAdd)
			$Model->scenario = 'Add';
		elseif ($btnEdit || $btnSaveEdit)
			$Model->scenario = 'Edit';
		elseif ($btnDelete)
			$Model->scenario = 'Delete';
		elseif ($btnResendActivationLink)
			$Model->scenario = 'ResetActivationLink';
		elseif ($btnPrimary)
			$Model->scenario = 'SetAsPrimary';

		$ID = \GPCS::POST('hdnEmailID');
		if ($btnDelete && !$ID) {	//Delete button of the edit form. We will not assign whole form
			$ID = \GPCS::POST('ProfileInfo');
			$ID = $ID ? $ID['hdnEmailID'] : $ID;
		}
		if ($ID)
			$Model->attributes = array('hdnEmailID' => $ID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('ProfileInfo');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();
		elseif ($btnResendActivationLink)
			$Model->ResetActivationLink();
		elseif ($btnPrimary)
			$Model->SetAsPrimary();
		else
			\Base\FormModel::AjaxValidation('ProfileInfo', $Model, true);

		if ($ActivationCode = $Model->ActivationCode) {
			if (!\Site\models\User\Activation::SendActivationEmail(
							$ActivationCode
							, $Model->ActivationEmail
							, $Model->Username
							, false)) {
				T\Msg::GMsg_Add(\t2::Site_User("Failed to send activation link!"), T\Msg::ErrorCSS);
				T\Msg::GMsg_Show(T\Msg::Prompt_Error);
			}
		}

		\Output::Render($this->controller
				, ($btnEdit ?
						'editinfo/emails_addedit' : 'editinfo/emails')
				, array(
			'Model' => $Model,
				)
		);
	}

}
