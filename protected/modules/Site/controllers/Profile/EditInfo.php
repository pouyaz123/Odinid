<?php

namespace Site\controllers\Profile;

use \Site\models\User\Login;
use Site\models\User\Register;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditInfo extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common'
						, \t2::Site_User('Basic info'));

		$Model = new \Site\models\Profile\Info('Edit');
		$Model->Username = Login::GetSessionDR('Username');

		$Model->Attach_User();
		switch ($Model->drUser['AccountType']) {
			case Register::UserAccountType_Artist:
				$Model->Attach_Artist();
				break;
			case Register::UserAccountType_Company:
				$Model->Attach_Company();
				break;
		}

		if (\GPCS::POST('btnUpdate')) {
			$Model->attributes = \GPCS::POST('ProfileInfo');
			$Model->Save();
		} else {
			$Model->SetForm();
		}

		\Output::Render($this->controller
				, ($Model->drUser['AccountType'] === Register::UserAccountType_Artist ?
						'editinfo/artist' : 'editinfo/company')
				, array(
			'Model' => $Model,
				)
		);
	}

}
