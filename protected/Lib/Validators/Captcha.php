<?php

namespace Validators;

/**
 * Description of Captcha Validator extends \CCaptchaValidator
 *
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Captcha extends \CCaptchaValidator {

	protected function validateAttribute($object, $attribute) {
		if (property_exists($object, "DontValidateCaptcha") && $object->DontValidateCaptcha)
			return;
		parent::validateAttribute($object, $attribute);
	}

}
