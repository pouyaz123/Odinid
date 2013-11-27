<?php

namespace Admin\Components;

use Admin\Consts\Routes as Routes;

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

	static function AjaxValidation($AjaxKW, \Base\FormModel $Model, $DontValidateCaptcha = false, $AjaxKWPostName = 'ajax') {
		if (\GPCS::POST($AjaxKWPostName) == $AjaxKW) {
			$Model->DontValidateCaptcha = $DontValidateCaptcha;
			echo \CActiveForm::validate($Model);
			\Yii::app()->end();
		}
	}

	/**
	 * sets the admin internal environment such as the context menu or required scripts
	 */
	function SetInternalEnv() {
		\Output::AddIn_GeneralOutput(function($obj) {
					\Base\DataGrid::LoadFiles();

					$lnk = function($LngCat, $Label, $Route, $arrHtmlOptions = array()) {
								return \CHtml::link(\Lng::Admin($LngCat, $Label), $Route, $arrHtmlOptions);
							};
					$spn = function($Lable) {
								return "<span>$Lable</span>";
							};
					$obj->menu = array(
						array('text' => $lnk('User', 'Logout', Routes::Logout, array('rel' => \html::AjaxExcept))),
						array('text' => $lnk('User', 'Cartable', Routes::Cartable)),
						array('text' => \Lng::Admin('Modules', $spn('Users')),
							'children' => array(
								array('text' => $lnk('Modules', 'User types', Routes::User_Types)),
								array('text' => $lnk('Modules', 'Invitations', Routes::User_Invitations)),
								array('text' => $lnk('Modules', 'User plans', Routes::User_Plans)),
								array('text' => $lnk('Modules', 'User list', Routes::User_List)),
							)
						),
					);
				}, $this);
//		$arrHtmlOptions = array(
//			'ajax' => array(
//				'url'=>Routes::Logout,
//				'update' => '#divContent',
//			)
//		);
//		$this->menu = array(
//			array('label' => \Lng::Admin('User', 'Logout'), 'url' => \Admin\Consts\Routes::Logout),
//			array('label' => \Lng::Admin('Modules', 'Invitations'), 'url' => \Admin\Consts\Routes::Invitations,
//				'items' => array(
//					array('label' => \Lng::Admin('Common', 'List'), 'url' => \Admin\Consts\Routes::Invitation_List),
//					array('label' => \Lng::Admin('Modules', 'Create Invitation'), 'url' => \Admin\Consts\Routes::Invitation_AddEdit),
//				)
//			),
//		);
	}

}