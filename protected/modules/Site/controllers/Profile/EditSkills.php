<?php

namespace Site\controllers\Profile;

use \Site\models\User\Login;
use Site\models\Profile\Skills;

/**
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class EditSkills extends \CAction {

	public function run() {
		$this->controller->pageTitle = \t2::SitePageTitle('tr_common', \t2::Site_User('Skills'));
\html::TagIt_Load();
\html::LoadJS('balloon/jquery.balloon.min');
		$Model = new Skills();
		Skills::$UserID = Login::GetSessionDR('ID');
		if (\GPCS::POST('btnSaveEdit')) {
			$SkillsPost = \GPCS::POST($Model->PostName);
			$Skills = $SkillsPost['txtSkills'];
			if ($Skills) {
				$arrSkills = explode(',', $Skills);
				foreach ($arrSkills as $idx => $Skill) {
					$Model->attributes = array(
						'txtSkills' => $Skills,
						'txtSkill' => $Skill,
						'ddlRate' => isset($SkillsPost['ddlRate']) ? $SkillsPost['ddlRate'][$idx] : null
					);
					$Model->PushTransactions();
				}
			}
			Skills::Commit();
		}



		\Output::Render($this->controller
				, 'editinfo/skills'
				, array(
			'Model' => $Model,
				)
		);
	}

}
