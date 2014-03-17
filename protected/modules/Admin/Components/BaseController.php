<?php

namespace Admin\Components;

use \Admin\Consts\Routes as Routes;
use \Tools as T;

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BaseController extends \CController {

	public $defaultAction = 'default';

	/**
	 * @var string the default layout for the controller view.
	 */
	public $layout = 'outerpages';

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
	 * sets the admin internal(loggedin) environment such as default layout or the context menu or ...
	 */
	private function SetInternalEnv() {
		//side menu
		\Output::AddIn_GeneralOutput(function($obj) {
			\Base\DataGrid::LoadFiles();

			$lnk = function($LngCat, $Label, $Route, $arrHtmlOptions = array()) {
				return \CHtml::link(\t2::Admin($LngCat, $Label), $Route, $arrHtmlOptions);
			};
			$spn = function($Lable) {
				return "<span>$Lable</span>";
			};
			$obj->menu = array(
				array('text' => $lnk('tr_common', 'Logout', Routes::Logout, array('rel' => \html::AjaxExcept))),
				array('text' => $lnk('tr_common', 'Cartable', Routes::Cartable)),
				array('text' => $spn(\t2::Admin_Common('Users')),
					'children' => array(
						array('text' => $lnk('tr_common', 'User types', Routes::User_Types)),
						array('text' => $lnk('tr_common', 'Invitations', Routes::User_Invitations)),
						array('text' => $lnk('tr_common', 'User plans', Routes::User_Plans)),
						array('text' => $lnk('tr_common', 'User list', Routes::User_List)),
					)
				),
			);
		}, $this);
		if (T\HTTP::IsAsync()) {
			$URL = explode('?', $_SERVER['REQUEST_URI']);
			$URL = $URL[0];
			\html::InlineJS("$('#widMenu .selected').removeClass('selected');$('#widMenu a[href|=\"$URL\"]').addClass('selected')", 'SideMenuUpdate', '');
		}
	}

	/**
	 * set appropriate environment such as layer for internal loggedin versus external admin not-loggedin
	 */
	function SetEnv() {
		if (\Admin\models\AdminLogin::IsLoggedIn()) {
			$this->layout = 'innerpages';
			$this->SetInternalEnv();
		} else
			$this->layout = 'outerpages';
	}

	/**
	 * admin panel needs authentication and authorization by default. Handled by this filter.
	 * @return array
	 */
	public function filters() {
		return array(
			array(
				'\Admin\filters\AdminAuth',
			)
		);
	}

}
