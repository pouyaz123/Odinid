<?php

namespace Site\Components;

use Site\Consts\Routes;
use \Site\models\User\Login;
use Tools as T;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class HeaderNav {

	static public function GetInstance() {
		return new static;
	}

	public function __toString() {
		$ctrl = \Yii::app()->controller;
		$NavItems = array(
			array('label' => \t2::site_site('Home'), 'url' => $ctrl->createAbsoluteUrl(Routes::Home)),
		);
		if (Login::IsLoggedIn()) {
			$Username = Login::GetSessionDR('Username');
			$arrUsrPanelItems = array(
				array('label' => \CHtml::encode(T\String::ucwords_ASCIISafe($Username))
					, 'url' => array(Routes::User_Profile(\CHtml::encode($Username)))),
				array('label' => \t2::site_site('Logout')
					, 'url' => array(Routes::UserLogout)
					, 'linkOptions' => array('rel' => \html::AjaxExcept)),
			);
			$NavItems[] = array('label' => \t2::site_site('Add Project'), 'url' => Routes::User_EditPrj());
		} else {
			$arrUsrPanelItems = array(
				array('label' => \t2::site_site('Sign in')
					, 'url' => array(Routes::UserLogin)),
				array('label' => \t2::site_site('Sign up')
					, 'url' => array(Routes::UserRegister)),
			);
		}
		$Result = $ctrl->widget(
						'zii.widgets.CMenu'
						, array(
					'items' => $NavItems,
					'id' => 'mnuHdrNav',
					'htmlOptions' => array('rel' => \html::AjaxLinks('#divContent:insert'))
						)
						, true
				)
				. ' ' //to have a tiny html space
				. $ctrl->widget('zii.widgets.CMenu', array(
					'items' => $arrUsrPanelItems,
					'id' => 'mnuUsrPanel',
					'htmlOptions' => array('rel' => \html::AjaxLinks('#divContent:insert'))
						), true);
		return $Result;
	}

}
