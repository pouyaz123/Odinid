<?php

namespace Site\controllers\EditProfile;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Setting extends \CAction {

	public function run() {
		$Model = new \Site\models\Profile\Setting;
		$Model->UserID = \Site\models\User\Login::GetSessionDR('ID');
		if (\GPCS::POST('btnUpdate')) {
			$Model->attributes = \GPCS::POST('UserSetting');
			$Model->Save();
		} else {
			$Model->SetForm();
		}
		\Output::Render($this->controller
				, 'editinfo/setting'
				, array(
			'Model' => $Model,
				)
		);
	}

}
