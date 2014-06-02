<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use \Site\models\Profile\Languages as Model;
use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Languages extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Languages'));
		\html::TagIt_Load();
		\html::Balloon_Load();
		\html::jqUI_AutoComplete_Load();
		$Model = new Model();
		Model::$UserID = Login::GetSessionDR('ID');
		if (\GPCS::POST('btnSaveEdit')) {
			$Post = \GPCS::POST($Model->PostName);
			$Items = $Post['txtLanguages'];
			if ($Items) {
				$Items = trim($Items, ",\t\n\r\0\x0B");
				$arrItems = explode(',', $Items);
				foreach ($arrItems as $idx => $Item) {
					$Model->attributes = array(
						'txtLanguages' => $Items,
						'txtLanguage' => $Item,
						'ddlRate' => isset($Post['ddlRate']) ? $Post['ddlRate'][$idx] : null
					);
					$Model->PushTransactions();
				}
			}
			Model::Commit();
		}
		\Output::AddIn_AjaxOutput(function() {
			$term = \GPCS::GET('term')? : \GPCS::POST('term');
			if ($term) {
				$Items = T\DB::GetField("SELECT GROUP_CONCAT(`Language` ORDER BY `IsOfficial` DESC, `Language` SEPARATOR ',')"
								. " FROM `_languages`"
								. " WHERE `Language` LIKE CONCAT(:term, '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
								, array(':term' => T\DB::EscapeLikeWildCards(mb_convert_encoding($term, 'UTF8', 'UTF8'))));
				if ($Items)
					echo json_encode(explode(',', $Items));
			}
		}, 'AutoComplete_UserLanguages_txtLanguages');

		\Output::Render($this->controller
				, 'editinfo/languages'
				, array(
			'Model' => $Model,
				)
		);
	}

}
