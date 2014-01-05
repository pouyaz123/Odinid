<?php

namespace Site\controllers\User;

/**
 * Description of LoginAction
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class Login extends \CAction {

	public function run($invitationCode = null) {
//		print_r(\Yii::app()->db->createCommand('select langcode, :lng as lng from {{languages_site}} where `LangCode`=:lng and `LangCode`=:lng limit 5')->queryAll(true, array(':lng'=>'Ch')));
		$this->controller->render('login');
	}

}
