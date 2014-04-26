<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use Site\models\User\Register;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Avatar extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Avatar'));
		\Tools\Cloudinary\Cloudinary::Load();
		\html::jCrop_Load();
		$Model = new \Site\models\Profile\Avatar();
		$Model->UserID = Login::GetSessionDR('ID');
		if (\GPCS::POST('btnUpload')) {
			$Model->scenario = 'Upload';
			$Model->Upload();
		} elseif (\GPCS::POST('btnDelete')) {
			$Model->scenario = 'Delete';
			$Model->Delete();
		} elseif (\GPCS::POST('btnCrop')) {
			$Model->scenario = 'Crop';
			$Model->attributes = \GPCS::POST($Model->PostName);
			$Model->Crop();
		} else {
			$Model->SetForm();
		}

		\Output::Render($this->controller
				, 'editinfo/avatar'
				, array(
			'Model' => $Model,
				)
		);
	}

}
