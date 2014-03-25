<?php

namespace Site\controllers\Profile;

use \Consts as C;
use \Tools as T;
use \Site\models\User\Login;
use Site\models\User\Register;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditEmails extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common'
						, \t2::Site_User('Emails'));

		$Model = new \Site\models\Profile\Info();
		$Model->Username = Login::GetSessionDR('Username');

		$Model->Attach_Emails();

		$btnAdd = \GPCS::POST('btnAdd');
		$btnSaveEdit = \GPCS::POST('btnSaveEdit');
		$btnEdit = \GPCS::POST('btnEdit');
		$btnDelete = \GPCS::POST('btnDelete');
		$btnResendActivationLink = \GPCS::POST('btnResendActivationLink');

		if ($btnAdd)
			$Model->scenario = 'Add';
		elseif ($btnEdit || $btnSaveEdit)
			$Model->scenario = 'Edit';
		elseif ($btnDelete)
			$Model->scenario = 'Delete';
		elseif ($btnResendActivationLink)
			$Model->scenario = 'ResetActivationLink';


		if ($EmailID = \GPCS::POST('hdnEmailID'))
			$Model->attributes = array('hdnEmailID' => $EmailID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('ProfileInfo');
			$Model->Save();
			$Model->SetForm();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();
		elseif ($btnResendActivationLink)
			$Model->ResetActivationLink();
		else
			\Base\FormModel::AjaxValidation('ProfileInfo', $Model, true);

		if ($ActivationCode = $Model->ActivationCode) {
			\Site\models\User\Activation::SendActivationEmail(
					$ActivationCode
					, $Model->ActivationEmail
					, $Model->Username);
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
