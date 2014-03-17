<?php

namespace Site\Components;

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BaseController extends \CController {

	public $defaultAction = 'default';

	/**
	 * WARNING ! has been changed in SetEnv
	 * @var string 
	 */
	public $layout = 'visitors';

	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array();

	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array(); //breadcrumbs is used in main layout to build path links

	/**
	 * set appropriate environment such as layer for users versus visitors
	 */
	public function SetEnv() {
		if (\Site\models\User\Login::IsLoggedIn())
			$this->layout = 'users';
		else
			$this->layout = 'visitors';
	}

}
