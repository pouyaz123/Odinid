<?php

namespace Site\controllers\EditProfile;

use \Site\models\User\Login;
use Site\models\Profile\WorkFields as Model;
use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class WorkFields extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle(\t2::site_site('Work fields'));
		\html::TagIt_Load();
		\html::jqUI_AutoComplete_Load();
		$Model = new Model();
		Model::$UserID = Login::GetSessionDR('ID');
		if (\GPCS::POST('btnSaveEdit')) {
			$Post = \GPCS::POST($Model->PostName);
			$Items = $Post['txtWorkFields'];
			if ($Items) {
				$Items = trim($Items, ",\t\n\r\0\x0B");
				$arrItems = explode(',', $Items);
				foreach ($arrItems as $idx => $Item) {
					$Model->attributes = array(
						'txtWorkFields' => $Items,
						'txtWorkField' => $Item,
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
				$Items = T\DB::GetField("SELECT GROUP_CONCAT(`WorkField` ORDER BY `IsOfficial` DESC, `WorkField` SEPARATOR ',')"
								. " FROM `_workfields`"
								. " WHERE `WorkField` LIKE CONCAT(:term, '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
								, array(':term' => T\DB::EscapeLikeWildCards(mb_convert_encoding($term, 'UTF8', 'UTF8'))));
				if ($Items)
					echo json_encode(explode(',', $Items));
			}
		}, 'AutoComplete_UserWorkFields_txtWorkFields');

		\Output::Render($this->controller
				, 'editinfo/workfields'
				, array(
			'Model' => $Model,
				)
		);
	}

}
