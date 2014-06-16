<?php

namespace Site\controllers\EditProjects;

use \Site\models\User\Login;
use \Site\models\Projects\Projects;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Prj_Base extends \CAction {

	protected $Type = Projects::Type_Project;

	public function run() {
		$ID = \GPCS::GET('id')? : \GPCS::POST('ID');
		#title
		switch ($this->Type) {
			case Projects::Type_Project:
				$Title = 'Project';
				break;
			case Projects::Type_Blog:
				$Title = 'Blog';
				break;
			case Projects::Type_Tutorial:
				$Title = 'Tutorial';
				break;
		}
		$Title = (!$ID ? "Add " : "Edit ") . $Title;
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site($Title));
		\html::TagIt_AC_Lib_Load();
		\html::jqUI_AutoComplete_Load();

		$Model = new Projects('Add');
		$Model->UserID = Login::GetSessionDR('ID');
		$Model->Type = $this->Type;
		if ($ID)
			$Model->hdnID = $ID;

		$btnAdd = \GPCS::POST('btnAdd');
		$btnSaveEdit = \GPCS::POST('btnSaveEdit');
		$btnUpload = \GPCS::POST('btnUpload');
		$btnCrop = \GPCS::POST('btnCrop');
		$btnDeleteThumb = \GPCS::POST('btnDeleteThumb');
		$btnDelete = \GPCS::POST('btnDelete');

		if ($btnAdd)
			$Model->scenario = 'Add';
		elseif ($ID || $btnSaveEdit)		//setting the values of or saving the edit form
			$Model->scenario = 'Edit';

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('Prj');
			$Model->Save();
		} elseif ($btnUpload)
			$Model->UploadThumb();
		elseif ($btnCrop)
			$Model->CropThumb();
		elseif ($btnDeleteThumb)
			$Model->DeleteThumb();
		elseif ($btnDelete)
			$Model->Delete();
		elseif ($ID)
			$Model->SetForm();

		\Output::Render($this->controller, '/editprojects/prj_form'
				, array(
			'Model' => $Model,
				)
		);
	}

}
