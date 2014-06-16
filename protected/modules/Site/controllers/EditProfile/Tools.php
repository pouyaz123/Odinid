<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use Site\models\Profile\Tools as Model;
use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Tools extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Tools'));
		\html::TagIt_AC_Lib_Load();
		\html::Balloon_Load();
		\html::jqUI_AutoComplete_Load();
		$Model = new Model();
		Model::$UserID = Login::GetSessionDR('ID');
		if (\GPCS::POST('btnSaveEdit')) {
			$Post = \GPCS::POST($Model->PostName);
			$Items = $Post['txtTools'];
			if ($Items) {
				$Items = trim($Items, ",\t\n\r\0\x0B");
				$arrItems = explode(',', $Items);
				foreach ($arrItems as $idx => $Item) {
					$Model->attributes = array(
						'txtTools' => $Items,
						'txtTool' => $Item,
						'ddlRate' => isset($Post['ddlRate']) ? $Post['ddlRate'][$idx] : null
					);
					$Model->PushTransactions();
				}
			}
			Model::Commit();
		}
		\Output::AddIn_AjaxOutput(function() {
			echo \Site\models\Profile\Tools::AC_GetSuggestions(\GPCS::GET('term')? : \GPCS::POST('term'));
		}, 'AutoComplete_UserTools_txtTools');

		\Output::Render($this->controller
				, 'editinfo/tools'
				, array(
			'Model' => $Model,
				)
		);
	}

}
