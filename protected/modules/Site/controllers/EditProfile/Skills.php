<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use \Site\models\Profile\Skills as Model;
use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Skills extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Skills'));
		\html::TagIt_AC_Lib_Load();
		\html::Balloon_Load();
		\html::jqUI_AutoComplete_Load();
		$Model = new Model();
		Model::$UserID = Login::GetSessionDR('ID');
		if (\GPCS::POST('btnSaveEdit')) {
			$Post = \GPCS::POST($Model->PostName);
			$Items = $Post['txtSkills'];
			if ($Items) {
				$Items = trim($Items, ",\t\n\r\0\x0B");
				$arrItems = explode(',', $Items);
				foreach ($arrItems as $idx => $Item) {
					$Model->attributes = array(
						'txtSkills' => $Items,
						'txtSkill' => $Item,
						'ddlRate' => isset($Post['ddlRate']) ? $Post['ddlRate'][$idx] : null
					);
					$Model->PushTransactions();
				}
			}
			Model::Commit();
		}
		\Output::AddIn_AjaxOutput(function() {
			echo \Site\models\Profile\Skills::AC_GetSuggestions(\GPCS::GET('term')? : \GPCS::POST('term'));
		}, 'AutoComplete_UserSkills_txtSkills');

		\Output::Render($this->controller
				, 'editinfo/skills'
				, array(
			'Model' => $Model,
				)
		);
	}

}
