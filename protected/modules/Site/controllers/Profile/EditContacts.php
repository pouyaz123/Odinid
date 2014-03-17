<?php

namespace Site\controllers\Profile;

use \Consts as C;
use \Tools as T;
use \Site\models\User\Login;
use Site\models\User\Register;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditContacts extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common'
						, \t2::Site_User('Edit contact info'));

		$Model = new \Site\models\Profile\Info();
		$Model->Username = Login::GetSessionDR('Username');

		$Model->AttachInfo_Contacts();

		$btnAdd = \GPCS::POST('btnAdd');
		$btnEdit = \GPCS::POST('btnEdit');
		$btnSaveEdit = \GPCS::POST('btnSaveEdit');
		$btnDelete = \GPCS::POST('btnDelete');

		if ($btnAdd)
			$Model->scenario = 'Add';
		elseif ($btnEdit || $btnSaveEdit)
			$Model->scenario = 'Edit';
		elseif ($btnDelete)
			$Model->scenario = 'Delete';

		if ($ContactID = \GPCS::POST('hdnContactID'))
			$Model->attributes = array('hdnContactID' => $ContactID);

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('ProfileInfo');
			$Model->Save();
		} elseif ($btnEdit)
			$Model->SetForm();
		elseif ($btnDelete)
			$Model->Delete();
		else {
			\Base\FormModel::AjaxValidation('ProfileInfo', $Model, true);
		}
		if ($ActivationCode = $Model->ActivationCode) {
//			\Site\models\User\Activation::SendActivationEmail(
//					$ActivationCode
//					, $Model->txtEmail
//					, $Model->Username);
		}

		\Output::Render($this->controller
				, ($btnEdit ?
						'editinfo/contacts_addedit' : 'editinfo/contacts')
				, array(
			'Model' => $Model,
				)
		);
	}

}
