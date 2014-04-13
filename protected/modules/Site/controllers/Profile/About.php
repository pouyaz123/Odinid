<?php

namespace Site\controllers\Profile;

use \Tools as T;
use Site\models\User\Register;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class About extends \CAction {

	public function run() {
		$Username = T\Basics::ucwords_ASCIISafe(\GPCS::GET('username'));
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common', \CHtml::encode($Username));

		$Model = new \Site\models\Profile\Info('viewInfo');
		$Model->Username = $Username;
		$Model->Attach_User();
		$Model->Attach_Contacts();
		$Model->Attach_WebAddresses();
		$Model->Attach_Locations();
		switch ($Model->drUser->AccountType) {
			case Register::UserAccountType_Artist:
				$Model->Attach_Artist();
				break;
			case Register::UserAccountType_Company:
				$Model->Attach_Company();
				break;
		}
		\Output::Render($this->controller
				, ($Model->drUser->AccountType === Register::UserAccountType_Artist ?
						'about_artist' : 'about_company')
				, array(
			'Model' => $Model,
				)
		);
	}

}
