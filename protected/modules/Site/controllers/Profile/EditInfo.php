<?php

namespace Site\controllers\Profile;

use \Site\models\User\Login;
use Site\models\User\Register;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditInfo extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Basic info'));
		$FormMode = \GPCS::GET('mode');

		$Model = new \Site\models\Profile\Info($FormMode == 'availability' ? 'EditAvailability' : 'EditBasicInfo');
		$Model->Username = Login::GetSessionDR('Username');

		if (!\GPCS::GET('mode'))
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

		//view file
		if ($Model->drUser['AccountType'] === Register::UserAccountType_Artist) {
			switch ($FormMode) {
				case 'availability':
					$viewFile = 'editinfo/availability';
					break;
				default:
					$viewFile = 'editinfo/artist';
					break;
			}
		} else {
			$viewFile = 'editinfo/company';
		}
		\Output::Render($this->controller
				, $viewFile
				, array(
			'Model' => $Model,
				)
		);
	}

}
