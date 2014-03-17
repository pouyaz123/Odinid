<?php

namespace Site\controllers\Profile;

use \Consts as C;
use \Tools as T;
use \Site\models\User\Login;
use Site\models\User\Register;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditInfo extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common'
						, \t2::Site_User('Edit basic info'));

		$Model = new \Site\models\Profile\Info('Edit');
		$Model->Username = Login::GetSessionDR('Username');

		$Model->AttachInfo_User();
		switch ($Model->drUser['AccountType']) {
			case Register::UserAccountType_Artist:
				$Model->AttachInfo_Artist();
				break;
			case Register::UserAccountType_Company:
				$Model->AttachInfo_Company();
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
