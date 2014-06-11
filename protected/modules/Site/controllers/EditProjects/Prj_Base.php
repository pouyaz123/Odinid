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
		$ID = \GPCS::GET('id');
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

		$Model = new Projects('Add');
		$Model->UserID = Login::GetSessionDR('ID');
		$Model->Type = $this->Type;

		$btnAdd = \GPCS::POST('btnAdd');
		$btnSaveEdit = \GPCS::POST('btnSaveEdit');
		$btnDelete = \GPCS::POST('btnDelete');

		if ($btnAdd)
			$Model->scenario = 'Add';
		elseif ($btnDelete)
			$Model->scenario = 'Delete';
		elseif ($ID || $btnSaveEdit)
			$Model->scenario = 'Edit';

		if ($btnAdd || $btnSaveEdit) {
			$Model->attributes = \GPCS::POST('Prj');
			$Model->Save();
		} elseif ($btnDelete)
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
