<?php

namespace Validators;

/**
 * DBExist extends \Validators\DBValidator and is a DB validator type
 *
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class DBExist extends \Validators\DBValidator {

	public function getCheckType() {
		return self::ExistCheck;
	}

}
