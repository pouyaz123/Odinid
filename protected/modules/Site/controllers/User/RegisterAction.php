<?php

namespace Site\controllers\User;

/**
 * Description of RegisterAction
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class RegisterAction extends \CAction {

	/**
	 * @param str $code invitation code from $_GET
	 */
	public function run($code = null) {
		$user = new \Site\models\User('Register');
		if ($code)
			$user->attributes = array('txtInvitationCode' => $code);
		\Base\FormModel::AjaxValidation('Register', $user, true);
		$ResgiterPost = \GPCS::POST('Register');
		if ($ResgiterPost) {
			$user->attributes = $ResgiterPost;
			$user->Register();
		}
		$this->controller->render('register', array('Model' => $user, 'InvitationCode' => $code));
	}

}

?>
