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
		$Result = $ctrl->widget(
				'zii.widgets.CMenu'
				, array(
			'items' => array(
				array('label' => \t2::site_site('Activity'), 'url' => $ctrl->createAbsoluteUrl(Routes::Home)),
				array('label' => 'About', 'url' => array('/site/page', 'view' => 'about')),
				array('label' => 'About', 'url' => array('/site/page', 'view' => 'about')),
				array('label' => 'About', 'url' => array('/site/page', 'view' => 'about')),
			),
			'id' => 'mnuHdrNav'
				)
				, true
		);
		$Result.=' '; //to have a tiny html space
		if (!Login::IsLoggedIn()) {
			$arrUsrPanelItems = array(
				array('label' => \t2::site_site('Sign in')
					, 'url' => array(Routes::UserLogin)),
				array('label' => \t2::site_site('Sign up')
					, 'url' => array(Routes::UserRegister)),
			);
		} else {
			$Username = Login::GetSessionDR('Username');
			$arrUsrPanelItems = array(
				array('label' => \CHtml::encode(T\String::ucwords_ASCIISafe($Username))
					, 'url' => array(Routes::User_Profile(\CHtml::encode($Username)))),
				array('label' => \t2::site_site('Logout')
					, 'url' => array(Routes::UserLogout)
					, 'linkOptions' => array('rel' => \html::AjaxExcept)),
			);
		}
		$Result.=$ctrl->widget('zii.widgets.CMenu', array(
			'items' => $arrUsrPanelItems,
			'id' => 'mnuUsrPanel',
			'htmlOptions' => array('rel' => \html::AjaxLinks('#divContent:insert'))
				), true);
		return $Result;
	}

}
