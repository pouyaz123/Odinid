<?php

namespace Site\models;

use \Consts as C;
use \Tools as T;

/**
 * model for _users
 * NOTE : scenario name has been used as the post name in ->PostName()
 * register, activation, login
 */
class User extends \Base\FormModel {

	public function PostName() {
		return $this->scenario;
	}

	public $txtEmail;
	public $txtEmailRepeat;
	public $txtUsername;
	public $txtPassword;
	public $txtInvitationCode;
	public $txtCaptcha;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('txtEmail, txtEmailRepeat, txtUsername, txtPassword, txtInvitationCode, txtCaptcha', 'required',
				'on' => 'Register'),
			array('txtInvitationCode', 'required',
				'on' => 'Register'),
			#
			array('txtEmail', 'email',
				'on' => 'Register'),
			array('txtEmailRepeat', 'compare',
				'compareAttribute' => 'txtEmail',
				'on' => 'Register'),
			array('txtEmail', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_user_info_ext` WHERE `Email`=:val LIMIT 1',
				'Msg' => '{attribute} "{value}" has been used previously.',
				'on' => 'Register'),
			#
			array('txtUsername', 'match', 'pattern' => C\Regexp::Username,
				'on' => 'Register'),
			array('txtUsername', 'length',
				'min' => C\Regexp::Username_MinLen, 'max' => C\Regexp::Username_MaxLen,
				'on' => 'Register'),
			array('txtUsername', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_users` WHERE `Username`=:val LIMIT 1',
				'Msg' => '{attribute} "{value}" has been used previously.',
				'on' => 'Register'),
			#
			array('txtPassword', 'length',
				'min' => C\Regexp::Password_MinLength,
				'on' => 'Register'),
			#
			array('txtCaptcha', 'captcha',
				'on' => 'Register'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'txtEmail' => \Lng::Site('User', 'Email'),
			'txtEmailRepeat' => \Lng::Site('User', 'Confirm email'),
			'txtUsername' => \Lng::Site('User', 'Username'),
			'txtPassword' => \Lng::Site('User', 'Password'),
			'txtInvitationCode' => \Lng::Site('User', 'Invitation code'),
			'txtCaptcha' => \Lng::Site('User', 'Verification code'),
		);
	}

	function Register() {
		if ($this->validate()) {
//			$db = \Yii::app()->db;
//			$trans = $db->beginTransaction();
//			$db->createCommand("INSERT INTO _users(`Username`, `Password`, `Status`, `RegisterTime`)
//						VALUES(:un, :pw, '" . C\User::Status_Pending . "', '" . gmdate('Y-m-d H:i:s') . "')")
//					->execute(array(':un' => $this->txtUsername, ':pw' => md5($this->txtPassword)));
//			$db->createCommand("INSERT INTO `_user_recoveries`()")
//		try{
//			$connection->createCommand($sql1)->execute();
//			$connection->createCommand($sql2)->execute();
//			//.... other SQL executions
//			$transaction->commit();
//		}
//		catch(Exception $e) // an exception is raised if a query fails{
//			$transaction->rollback();
//		}
		}
	}

}
