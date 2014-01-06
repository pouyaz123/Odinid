<?php

namespace Admin\Components;

use Admin\Consts\Routes as Routes;
use \Tools as T;

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BaseController extends \CController {

	public $defaultAction = 'default';

	/**
	 * @var string the default layout for the controller view.
	 * meaning the layout for logged in admins in the internal pages
	 */
	public $layout = 'innerpages';

	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array();

	/**
	 * sets the admin internal environment such as the context menu or required scripts
	 */
	function SetInternalEnv() {
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

}
