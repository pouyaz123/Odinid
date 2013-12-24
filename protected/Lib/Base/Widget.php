<?php

namespace Base;

/**
 * Extends \CWidget to support __toString magic function and other further features
 *
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Widget extends \CWidget {

	public function __toString() {
		return $this->run()? : '';
	}

}
