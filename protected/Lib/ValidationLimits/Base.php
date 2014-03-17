<?php

namespace ValidationLimits;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Base extends \CComponent {

	/**
	 * @staticvar null $ValLenghts
	 * @return \static
	 */
	static function GetInstance() {
		static $VR = null;
		if (!$VR)
			$VR = new static;
		return $VR;
	}

}
