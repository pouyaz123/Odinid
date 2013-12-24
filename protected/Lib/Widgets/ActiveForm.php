<?php

namespace Widgets;

/**
 * Description of ActiveForm extends \CActiveForm
 *
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class ActiveForm extends \CActiveForm {

	public function error($model, $attribute, $htmlOptions = array(), $enableAjaxValidation = false, $enableClientValidation = true) {
		return parent::error($model, $attribute, $htmlOptions, $enableAjaxValidation, $enableClientValidation);
	}

}
