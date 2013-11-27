<?php

namespace Site\models;

/**
 * model for _users
 * register, activation, login
 */
class UserLoginForm extends \CFormModel {

	public $Username;
	public $Password;
	public $RememberMe = false;
	private $_identity;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('Username, Password', 'required', 'on'=>'login, register'),
			array('Username', 'length', 'min' => 3, 'max' => 32, 'on'=>'login, register'),
			array('Password', 'authenticate', 'on'=>'login'),
			array('RememberMe', 'boolean', 'on'=>'login'),
				// The following rule is used by search().
				// @todo Please remove those attributes that should not be searched.
//			array('ID, UserTypeID, UserTypeExpDate, Username, Password, Status, PrimaryEmail, LastLoginIP, LastLoginTime, RegisterTime', 'safe', 'on' => 'search'),
		);
	}

	public function authenticate($attribute, $params) {
		$this->_identity = new \UserIdentity($this->Username, $this->Password);
		if (!$this->_identity->authenticate())
			$this->addError('password', 'Incorrect username or password');
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'Username' => 'Username',
			'Password' => 'Password',
		);
	}

}
