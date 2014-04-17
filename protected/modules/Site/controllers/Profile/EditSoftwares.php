<?php

namespace Site\controllers\Profile;

use \Site\models\User\Login;
use Site\models\Profile\Softwares;
use \Tools as T;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditSoftwares extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common', \t2::Site_User('Softwares'));
		\html::TagIt_Load();
		\html::Balloon_Load();
		\html::jqUI_AutoComplete_Load();
		$Model = new Softwares();
		Softwares::$UserID = Login::GetSessionDR('ID');
		if (\GPCS::POST('btnSaveEdit')) {
			$Post = \GPCS::POST($Model->PostName);
			$Items = $Post['txtSoftwares'];
			if ($Items) {
				$arrItems = explode(',', $Items);
				foreach ($arrItems as $idx => $Item) {
					$Model->attributes = array(
						'txtSoftwares' => $Items,
						'txtSoftware' => $Item,
						'ddlRate' => isset($Post['ddlRate']) ? $Post['ddlRate'][$idx] : null
					);
					$Model->PushTransactions();
				}
			}
			Softwares::Commit();
		}
		\Output::AddIn_AjaxOutput(function() {
			$term = \GPCS::GET('term')? : \GPCS::POST('term');
			if ($term) {
				$Items = T\DB::GetField("SELECT GROUP_CONCAT(`Software` ORDER BY `IsOfficial` DESC, `Software` SEPARATOR ',')"
								. " FROM `_softwares`"
								. " WHERE `Software` LIKE CONCAT(:term, '%') ESCAPE '" . T\DB::LikeEscapeChar . "'"
								, array(':term' => T\DB::EscapeLikeWildCards($term)));
				if ($Items)
					echo json_encode(explode(',', $Items));
			}
		}, 'AutoComplete_UserSoftwares_txtSoftwares');

		\Output::Render($this->controller
				, 'editinfo/softwares'
				, array(
			'Model' => $Model,
				)
		);
	}

}
